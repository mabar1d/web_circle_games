<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class ApiCircleGamesHelper
{
    public static function sendRequestApi($methodHttp, $methodUrl, $headers, $paramsBody)
    {
        $client = new Client();
        $headers["Authorization"] = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpbWFiYXIudmlkaXdpamF5YS5teS5pZFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjU3NTE2NiwiZXhwIjoxNjQyNTc4NzY2LCJuYmYiOjE2NDI1NzUxNjYsImp0aSI6IjdReU5ZVFpqNllTWE0yT1IiLCJzdWIiOjIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.HbG1Si9xOioXy1ho3t6H-_pvYsj9gRIKGotRs74T3D4';
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
