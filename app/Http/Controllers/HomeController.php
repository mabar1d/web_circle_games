<?php

namespace App\Http\Controllers;

use App\Helpers\ApiCircleGamesHelper;
use App\Models\NewsModel;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController
{

    public function index()
    {
        $listNews = NewsModel::where("status", 1)
            ->orderBy("created_at", "DESC")
            ->limit(4)
            ->get();
        return view('public.home', [
            "listNews" => $listNews
        ]);
    }
}
