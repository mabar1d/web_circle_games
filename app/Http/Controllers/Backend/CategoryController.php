<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.category.index', []);
    }

    public function getDatatable(Request $request)
    {
        if (request()->ajax()) {
            $requestData = $request->input();
            $listNewsCategory = CategoryModel::select(
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
                $categoryId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : null;
                $isDisabled = false;
                $data = NULL;
                if ($categoryId) {
                    $isDisabled = true;
                    $data = CategoryModel::findOrFail($categoryId);
                }
                $throwData = [
                    "data" => $data,
                    "isDisabled" => $isDisabled
                ];
                return view('backend/category/modalFormAdd', $throwData);
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
            if (!isset($requestData["categoryTitle"]) || !$requestData["categoryTitle"]) {
                throw new Exception("Title Category is Empty!", 1);
            }
            $categoryId = isset($requestData["categoryId"]) && $requestData["categoryId"] ? $requestData["categoryId"] : NULL;
            $title = isset($requestData["categoryTitle"]) && $requestData["categoryTitle"] ? trim($requestData["categoryTitle"]) : NULL;
            $desc = isset($requestData["categoryDesc"]) && $requestData["categoryDesc"] ? trim($requestData["categoryDesc"]) : NULL;
            $status = isset($requestData["categoryStatus"]) && $requestData["categoryStatus"] ? $requestData["categoryStatus"] : 0;
            $dataStore = array(
                "name" => $title,
                "desc" => $desc,
                "status" => $status
            );
            if (!$categoryId) {
                $checkDataExist = CategoryModel::where("name", $title)->count();
                if ($checkDataExist != 0) {
                    throw new Exception("Category Already Exist!", 1);
                }
            }
            CategoryModel::updateOrCreate(
                [
                    "id" => $categoryId
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
            $categoryId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : NULL;
            if (!$categoryId) {
                throw new Exception("Category Id is Empty!", 1);
            }
            CategoryModel::findOrFail($categoryId)->delete();
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
            $getData = CategoryModel::select("id", "name")
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
