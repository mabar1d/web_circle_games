<?php

namespace App\Http\Controllers;

use App\Helpers\ApiCircleGamesHelper;
use App\Models\NewsModel;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController
{

    public function index()
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $paramsBody = [
            'user_id' => NULL,
            'search' => NULL,
            'offset' => 0,
            'limit' => 4
        ];

        $getListNewsApi = ApiCircleGamesHelper::sendRequestApi("POST", "getListNewsWeb", $headers, $paramsBody);
        $getListNewsResponse = json_decode($getListNewsApi, true);
        if ($getListNewsResponse['code'] == 00) {
            $listNews = $getListNewsResponse['data'];
        }
        // dd($listNews);
        return view('public.layout', [
            "listNews" => $listNews
        ]);
    }
}
