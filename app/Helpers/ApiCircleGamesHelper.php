<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class ApiCircleGamesHelper
{
    public static function sendRequestApi($methodHttp, $methodUrl, $headers, $paramsBody)
    {
        $client = new Client();
        $url = env('URL_API_CIRCLE_GAMES') . 'api/' . $methodUrl;
        $response = $client->request($methodHttp, $url, [
            'form_params' => $paramsBody,
            'headers' => $headers,
            'verify'  => false,
        ]);
        $responseBody = json_decode($response->getBody(), true);
        $responseApi = json_encode($responseBody);
        return $responseApi;
    }
}
