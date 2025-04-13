<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Sentry\Laravel\Features\Feature;
use Sentry\Util\JSON;
use stdClass;

class SalesforceHelper
{
    private static $writeLog = true;

    private $objectName = null;
    private $q = null;
    private $fromSF = false;
    private $q_date = null;
    private $column = null;
    private $limit = 0;
    private $select = '';
    private $sort = null;
    private $whereNull = null;
    private $whereNotNull = null;

    private $groupBy = null;
    private $whereIn = null;

    public function __construct($objectName)
    {
        $this->objectName = $objectName;
    }

    public static function table(string $objectName): SalesforceHelper
    {
        if (self::$writeLog) Log::info($objectName);
        return new SalesforceHelper($objectName);
    }

    public function get($column = null): Collection
    {
        if ($column) $this->column($column);
        $data = [];
        if ($this->q) $data['q'] = json_encode($this->q);
        if ($this->fromSF) $data['fromSF'] = 'true';
        if ($this->q_date) $data['q_date'] = json_encode($this->q_date);
        if ($this->column) $data['select'] = json_encode($this->column);
        if ($this->limit) $data['limit'] = $this->limit;
        if ($this->select) $data['raw'] = $this->select;
        if ($this->sort) $data['sort'] = $this->sort;
        if ($this->whereNull) $data['whereNull'] = json_encode($this->whereNull);
        if ($this->whereNotNull) $data['whereNotNull'] = json_encode($this->whereNotNull);
        if ($this->groupBy) {
            $data['groupBy'] = $this->groupBy;
            $data['select'] = json_encode([$this->groupBy]);
        }
        if ($this->whereIn) {
            $data['whereIn'] = json_encode($this->whereIn);
        }
        $response = Http::get('http://salesforce/salesforce/api/' . $this->objectName, $data);
        $response = json_decode($response->getBody()->getContents(), 1);
        if (self::$writeLog) Log::info($response);
        if (isset($response['success']) && $response['success'] === false)
            throw new Exception('Error getting ' . $this->objectName . ' - ' . $response['error']);
        if ($response === null) return collect([]);
        if ($this->groupBy) return collect($response);
        $response = $this->prepare($response);
        return $response;
    }

    public function first(): ?SalesforceHelperRecord
    {
        $this->limit = 1;
        $list = $this->get();
        if (count($list) > 0) return $list[0];
        return null;
    }

    public function groupBy($column): SalesforceHelper
    {
        $this->groupBy = $column;
        return $this;
    }

    public function take($limit): SalesforceHelper
    {
        $this->limit = $limit;
        return $this;
    }

    private function prepare($data): Collection
    {
        foreach ($data as $key => $value) {
            $data[$key] = new SalesforceHelperRecord($this->objectName, $value, 'get');
        }
        return collect($data);
    }

    public static function select(string $query): SalesforceHelper
    {
        $salesforce = new SalesforceHelper('0');
        $salesforce->select = $query;
        return $salesforce;
    }

    public function orderBy(string $column, $direction = 'asc'): SalesforceHelper
    {
        $this->sort = $column . ',' . $direction;
        return $this;
    }


    public function where($column, $q2, $q3 = null): SalesforceHelper
    {
        if ($q3) {
            if ($this->q) $this->q = array_merge($this->q, [[$column, $q2, $q3]]);
            else $this->q = [[$column, $q2, $q3]];
        } else {
            if ($this->q) $this->q = array_merge($this->q, [[$column, '=', $q2]]);
            else $this->q = [[$column, '=', $q2]];
        }
        return $this;
    }

    public function whereNull($column): SalesforceHelper
    {
        if ($this->whereNull) $this->whereNull = array_merge($this->whereNull, [$column]);
        else $this->whereNull = [$column];
        return $this;
    }

    public function whereNotNull($column): SalesforceHelper
    {
        if ($this->whereNotNull) $this->whereNotNull = array_merge($this->whereNotNull, [$column]);
        else $this->whereNotNull = [$column];
        return $this;
    }

    public function fromSF(): SalesforceHelper
    {
        $this->fromSF = true;
        return $this;
    }

    public function whereDate($column, $q2, $q3 = null): SalesforceHelper
    {
        if ($q3) {
            if ($this->q_date) $this->q_date = array_merge($this->q_date, [[$column, $q2, $q3]]);
            else $this->q_date = [[$column, $q2, $q3]];
        } else {
            if ($this->q_date) $this->q_date = array_merge($this->q_date, [[$column, '=', $q2]]);
            else $this->q_date = [[$column, '=', $q2]];
        }
        return $this;
    }

    public function whereIn(string $column, array $q): SalesforceHelper
    {
        if ($this->whereIn) $this->whereIn = array_merge($this->whereIn, [[$column, $q]]);
        else $this->whereIn = [[$column, $q]];
        return $this;
    }

    public function column($column): SalesforceHelper
    {
        if (is_string($column)) {

            $this->column = [$column];
        }
        if (is_array($column)) {
            $this->column = $column;
        }
        if (!isset($this->column['Id'])) $this->column[] = 'Id';
        return $this;
    }


    /////////////////////////////////////////////////////////

    public function post(array $data): string
    {
        $response = Http::post('http://salesforce/salesforce/api/' . $this->objectName, $data);
        $response = json_decode($response->getBody()->getContents());
        if (isset($response->success) && $response->success === false)
            throw new Exception('Error create ' . $this->objectName . ' - ' . (isset($response->message) ? $response->message : ''));
        $id = $response->data->Id;
        return $id;
    }

    public function insert(array $data): string
    {
        return $this->post($data);
    }

    ////////////////////////////////////////////////////////

    public function getById(string $id): stdClass | array
    {
        $this->q = [['Id', '=', $id]];
        $array = $this->get();
        if (count($array) === 0) throw new Exception('Error getting ' . $this->objectName);
        return $array[0];
    }

    //////////////////////////////////////////////////////

    public function update(string $id, array $data): void
    {
        $response = Http::put('http://salesforce/salesforce/api/' . $this->objectName . '/' . $id, $data);
        $response = json_decode($response->getBody()->getContents());
        if (isset($response->success) && $response->success === false)
            throw new Exception('Error updating ' . $this->objectName . ' - ' . $response->message);
    }


    //////////////////////////////////////////////////////

    public function delete(string $id): void
    {
        $response = Http::delete('http://salesforce/salesforce/api/' . $this->objectName . '/' . $id);
        $response = json_decode($response->getBody()->getContents());
        if (isset($response->success) && $response->success === false)
            throw new Exception('Error deleting ' . $this->objectName . ' - ' . $response->message);
    }
}
