<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class HttpHelper extends Http
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function get($service, $url, $data = [], $errorMessage = '', $ignoreExceptions = false)
    {
        if (!$errorMessage) $errorMessage = 'Error getting data - ' . $url;
        $response = Http::get('http://' . $service . '/' . $service . ($url === '' ? '' : '/') . $url, $data);
        $response = json_decode($response->getBody()->getContents());
        if ($ignoreExceptions) {
            return $response;
        }
        if (isset($response->success) && $response->success === false)
            throw new Exception($errorMessage);
        if (!isset($response->data)) return null;
        return $response->data;
    }

    public static function post($service, $url, $data = [], $errorMessage = '', $ignoreExceptions = false)
    {
        if (!$errorMessage) $errorMessage = 'Error posting data - ' . $url;
        $response = Http::post('http://' . $service . '/' . $service . ($url === '' ? '' : '/') . $url, $data);
        $response->getBody()->getContents();
        if ($ignoreExceptions) {
            Log::info($response);
            $response = json_decode($response);
            return $response;
        }
        $response = json_decode($response);
        if (isset($response->success) && $response->success === false)
            throw new Exception($errorMessage);
        if (!isset($response->data)) return null;
        return $response->data;
    }
    public static function put($service, $url, $data = [], $errorMessage = '', $ignoreExceptions = false)
    {
        if (!$errorMessage) $errorMessage = 'Error putting data - ' . $url;
        $response = Http::put('http://' . $service . '/' . $service . ($url === '' ? '' : '/') . $url, $data);
        $response = json_decode($response->getBody()->getContents());
        if ($ignoreExceptions) {
            return $response;
        }
        if (isset($response->success) && $response->success === false)
            throw new Exception($errorMessage);
        if (!isset($response->data)) return null;
        return $response->data;
    }
    public static function delete($service, $url, $data = [], $errorMessage = '', $ignoreExceptions = false)
    {
        if (!$errorMessage) $errorMessage = 'Error deleting data - ' . $url;
        $response = Http::delete('http://' . $service . '/' . $service . ($url === '' ? '' : '/') . $url, $data);
        $response = json_decode($response->getBody()->getContents());
        if ($ignoreExceptions) {
            return $response;
        }
        if (isset($response->success) && $response->success === false)
            throw new Exception($errorMessage);
        if (!isset($response->data)) return null;
        return $response->data;
    }

    public static function response($data): Response
    {
        return response($data, 200);
    }
}
