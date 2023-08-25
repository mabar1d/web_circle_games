<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiCircleGamesHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NewsCategoryController extends Controller
{
    public function __construct()
    {
        session(['menu' => 'master_news_category']);
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.newsCategory.index', []);
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

            $getListNewsCategoryApi = ApiCircleGamesHelper::sendRequestApi("POST", "getListNewsCategory", $headers, $paramsBody);
            $getListNewsCategoryResponse = json_decode($getListNewsCategoryApi, true);
            if ($getListNewsCategoryResponse['code'] == 00) {
                $listNewsCategory = $getListNewsCategoryResponse['data'];
            }
            return Datatables::of($listNewsCategory)
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
}
