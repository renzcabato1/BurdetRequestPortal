<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class SapApiController extends Controller
{
    //

    public static function readSapTableApi($connection, $table)
    {
        $client = new Client();
        // dd(env('SAP_API_URL'));
        $response = $client->request('GET','http://10.96.4.39:8012/api/read-table',['verify' => false], ['query' => ['connection' => $connection, 'table' => $table]]);
        return collect(json_decode($response->getBody()));
    }

    public static function executeSapFunction($connection, $function, $parameters, $return)
    {
        $client = new Client();
        // dd(env('SAP_API_URL'));
        $response = $client->request('POST', 'http://10.96.4.39:8012/api/execute-fm', [
            'form_params' => [
                'connection' => $connection,
                'function' => $function,
                'parameters' => $parameters,
                'return' => $return,
                'verify' => false,
            ]
        ]);
        return collect(json_decode($response->getBody()));
    }

}
