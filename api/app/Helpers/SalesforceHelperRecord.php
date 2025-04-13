<?php

namespace App\Helpers;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SalesforceHelperRecord
{
    private $config = [
        'objectName' => '',
        'Id' => '',
        'status' => 'get'
    ];
    private $data = [];
    private $dataForUpdate = [];
    public function __construct($objectName, $data = null, $status = 'new')
    {
        $this->config['objectName'] = $objectName;
        if ($data) $this->config['Id'] = $data['Id'];
        $this->config['status'] = $status;
        if ($data) $this->data = $data;
    }

    public function __get($column)
    {
        return $this->data[$column];
    }

    public function __set($column, $value)
    {
        $this->data[$column] = $value;
        $this->dataForUpdate[$column] = $value;
        if ($this->config['status'] === 'get') $this->config['status'] = 'update';
        return $this;
    }

    public function save()
    {
        $data = [...$this->dataForUpdate];

        if (isset($data['CreatedDate'])) unset($data['CreatedDate']);
        foreach ($data as $key => $value) {
            if ($this->validateDate($value)) {
                $data[$key] = date('Y-m-d\TH:i:s\Z', strtotime($value));
            }
        }
        if ($this->config['status'] === 'update') {
            $response = Http::put('http://salesforce/salesforce/api/' . $this->config['objectName'] . '/' .  $this->config['Id'], $data);
            $response = $response->getBody()->getContents();
            Log::info($response);
            $response = json_decode($response);
            if (isset($response->success) && $response->success === false)
                throw new Exception('Error updating ' . $this->config['objectName'] . ' - ' . $response->message);
        }

        if ($this->config['status'] === 'new') {

            $response = Http::post('http://salesforce/salesforce/api/' . $this->config['objectName'], $data);
            $response = $response->getBody()->getContents();
            Log::info($response);
            $response = json_decode($response);
            if (isset($response->success) && $response->success === false)
                throw new Exception('Error creating ' . $this->config['objectName'] . ' - ' . $response->message);

            $this->config['Id'] = $response->data->Id;
            $this->data = SalesforceHelper::table($this->config['objectName'])
                ->fromSF()
                ->where('Id', $this->config['Id'])
                ->first()
                ->getAll();
        }
        $this->config['status'] = 'get';
        return $this;
    }

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function getAll()
    {
        return $this->data;
    }
    public function toArray()
    {
        return $this->data;
    }
}
