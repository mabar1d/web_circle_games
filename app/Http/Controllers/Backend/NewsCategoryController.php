<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiCircleGamesHelper;
use App\Http\Controllers\Controller;
use App\Models\NewsCategoryModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NewsCategoryController extends Controller
{
    public function __construct()
    {
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

    public function getDropdownData(Request $request)
    {
        $response = array(
            "code" => 1,
            "message" => ""
        );
        DB::beginTransaction();
        try {
            $requestData = $request->input();
            $getData = NewsCategoryModel::select("id", "name")
                ->where("status", 1);
            if (isset($requestData["search"]) && $requestData["search"]) {
                $getData = $getData->where('name', 'like', '%' . $requestData["search"] . '%');
            }
            $getData = $getData->get();
            $response = array(
                "code" => 0,
                "message" => "Success Store Data",
                "data" => $getData->toArray()
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
