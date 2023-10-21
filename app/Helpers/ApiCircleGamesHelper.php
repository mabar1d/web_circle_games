<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class ApiCircleGamesHelper
{
    public static function sendRequestApi($methodHttp, $methodUrl, $headers, $paramsBody)
    {
        $client = new Client();
        $headers["Authorization"] = 'Bearer ' . env('GOD_BEARER_TOKEN');
        $url = env('URL_API_CIRCLE_GAMES') . 'api/' . $methodUrl;
        $response = $client->request($methodHttp, $url, [
            'form_params' => $paramsBody,
            'headers' => $headers,
            'verify'  => false,
        ]);
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody;
    }
}
