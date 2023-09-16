<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiCircleGamesHelper;
use App\Http\Controllers\Controller;
use App\Models\NewsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NewsController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.news.index', []);
    }

    public function getDatatable(Request $request)
    {
        if (request()->ajax()) {
            $requestData = $request->input();
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];

            $paramsBody = [
                'user_id' => 'web',
                'search' => NULL,
                'page' => 1
            ];

            $getListNewsApi = ApiCircleGamesHelper::sendRequestApi("POST", "getListNews", $headers, $paramsBody);
            $getListNewsResponse = json_decode($getListNewsApi, true);
            if ($getListNewsResponse['code'] == 00) {
                $listNews = $getListNewsResponse['data'];
            }
            // dd($listNews);
            return DataTables::of($listNews)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $result = isset($row['status']) && $row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-success">Active</span>';
                    return $result;
                })
                ->addColumn('action', function ($row) {
                    $result = "";
                    $result .= '<button type="button" class="btn btn-warning btn-xs m-1">View</button>';
                    $result .= '<button type="button" class="btn btn-danger btn-xs m-1">Delete</button>';
                    return $result;
                })
                ->rawColumns(['status', 'action'])
                ->make();
        }
    }

    public function getFormAdd(Request $request)
    {
        try {
            if (request()->ajax()) {
                $requestData = $request->input();
                return view('backend/news/modalFormAdd');
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        $response = array(
            "code" => 1,
            "message" => ""
        );
        DB::beginTransaction();
        try {
            $requestData = $request->input();
            if (!isset($requestData["newsTitle"]) || !$requestData["newsTitle"]) {
                throw new Exception("News Title is Empty!", 1);
            }
            if (!isset($requestData["newsCategory"]) || !$requestData["newsCategory"]) {
                throw new Exception("Category News is Empty!", 1);
            }
            $newsCategory = isset($requestData["newsCategory"]) && $requestData["newsCategory"] ? trim($requestData["newsCategory"]) : NULL;
            $title = isset($requestData["newsTitle"]) && $requestData["newsTitle"] ? trim($requestData["newsTitle"]) : NULL;
            $content = isset($requestData["newsContent"]) && $requestData["newsContent"] ? trim($requestData["newsContent"]) : NULL;
            $image = isset($requestData["newsImage"]) && $requestData["newsImage"] ? trim($requestData["newsImage"]) : NULL;
            $status = isset($requestData["newsStatus"]) && $requestData["newsStatus"] ? $requestData["newsStatus"] : 0;
            $tags = isset($requestData["newsTags"]) && $requestData["newsTags"] ? $requestData["newsTags"] : 0;
            $dataStore = array(
                "news_category_id" => $newsCategory,
                "title" => $title,
                "slug" => Str::slug($title),
                "content" => $content,
                "image" => $image,
                "status" => $status
            );
            $checkDataExist = NewsModel::where("title", $title)->count();
            if ($checkDataExist != 0) {
                throw new Exception("News Already Exist!", 1);
            }
            NewsModel::create($dataStore);
            $response = array(
                "code" => 0,
                "message" => "Success Store Data"
            );
            DB::commit();
        } catch (Exception $e) {
            $response = array(
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            );
            DB::rollBack();
        }
        return json_encode($response);
    }
}
