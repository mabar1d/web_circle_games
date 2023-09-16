<?php

namespace App\Http\Controllers\Backend;

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
            $listNewsCategory = NewsCategoryModel::select(
                'id',
                'name',
                'desc',
                'status'
            );
            return Datatables::of($listNewsCategory)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $result = isset($row['status']) && $row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-success">Active</span>';
                    return $result;
                })
                ->addColumn('action', function ($row) {
                    $result = "";
                    $result .= '<button type="button" class="btn btn-warning btn-xs m-1 btnView" data-id="' . $row['id'] . '">View</button>';
                    $result .= '<button type="button" class="btn btn-danger btn-xs m-1 btnDelete" data-id="' . $row['id'] . '">Delete</button>';
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
                $newsCategoryId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : null;
                $isDisabled = false;
                $data = NULL;
                if ($newsCategoryId) {
                    $isDisabled = true;
                    $data = NewsCategoryModel::findOrFail($newsCategoryId);
                }
                $throwData = [
                    "data" => $data,
                    "isDisabled" => $isDisabled
                ];
                return view('backend/newsCategory/modalFormAdd', $throwData);
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
            if (!isset($requestData["newsCategoryTitle"]) || !$requestData["newsCategoryTitle"]) {
                throw new Exception("Title News Category is Empty!", 1);
            }
            $newsCategoryId = isset($requestData["newsCategoryId"]) && $requestData["newsCategoryId"] ? $requestData["newsCategoryId"] : NULL;
            $title = isset($requestData["newsCategoryTitle"]) && $requestData["newsCategoryTitle"] ? trim($requestData["newsCategoryTitle"]) : NULL;
            $desc = isset($requestData["newsCategoryDesc"]) && $requestData["newsCategoryDesc"] ? trim($requestData["newsCategoryDesc"]) : NULL;
            $status = isset($requestData["newsCategoryStatus"]) && $requestData["newsCategoryStatus"] ? $requestData["newsCategoryStatus"] : 0;
            $dataStore = array(
                "name" => $title,
                "desc" => $desc,
                "status" => $status
            );
            if (!$newsCategoryId) {
                $checkDataExist = NewsCategoryModel::where("name", $title)->count();
                if ($checkDataExist != 0) {
                    throw new Exception("News Category Already Exist!", 1);
                }
            }
            NewsCategoryModel::updateOrCreate(
                [
                    "id" => $newsCategoryId
                ],
                $dataStore
            );
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

    public function delete(Request $request)
    {
        $response = array(
            "code" => 1,
            "message" => ""
        );
        DB::beginTransaction();
        try {
            $requestData = $request->input();
            $newsCategoryId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : NULL;
            if (!$newsCategoryId) {
                throw new Exception("News Category Id is Empty!", 1);
            }
            NewsCategoryModel::findOrFail($newsCategoryId)->delete();
            $response = array(
                "code" => 0,
                "message" => "Success Delete Data"
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
