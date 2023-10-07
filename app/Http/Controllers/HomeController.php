<?php

namespace App\Http\Controllers;

use App\Helpers\ApiCircleGamesHelper;
use App\Models\NewsModel;
use App\Models\VideoModel;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController
{

    public function index()
    {
        $listNews = NewsModel::where("status", 1)
            ->orderBy("created_at", "DESC")
            ->limit(4)
            ->get();
        $resultListNews = [];
        foreach ($listNews as $rowNews) {
            $rowNews["url_image"] = env('URL_API_CIRCLE_GAMES') . '/upload/news/' . $rowNews['image'];
            $rowNews["category_name"] = isset($rowNews->newsCategory) ? $rowNews->newsCategory->name : NULL;
            $resultListNews[] = $rowNews;
        }

        $listVideo = VideoModel::where("status", 1)
            ->orderBy("created_at", "DESC")
            ->limit(3)
            ->get();
        $resultListVideos = [];
        foreach ($listVideo as $rowVideo) {
            $rowVideo["youtube_embed"] = "https://www.youtube.com/embed/" . $rowVideo["link"] . "?loop=1";
            $rowVideo["url_image"] = "https://img.youtube.com/vi/" . $rowVideo["link"] . "/maxresdefault.jpg";
            $rowVideo["category_name"] = isset($rowVideo->category) ? $rowVideo->category->name : NULL;
            $resultListVideos[] = $rowVideo;
        }
        // dd($resultListVideos);
        return view('public.home', [
            "listNews" => $resultListNews,
            "listVideos" => $resultListVideos
        ]);
    }
}
