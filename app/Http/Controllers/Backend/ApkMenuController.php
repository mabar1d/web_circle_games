<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiCircleapkMenusHelper;
use App\Http\Controllers\Controller;
use App\Models\ApkMenuModel;
use App\Models\NewsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ApkMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.apkMenu.index', []);
    }

    public function getDatatable(Request $request)
    {
        if (request()->ajax()) {
            $requestData = $request->input();
            $listData = ApkMenuModel::select(
                'id',
                'title',
                'order',
                'status'
            );
            return Datatables::of($listData)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $result = isset($row['status']) && $row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
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
                $apkMenuId = isset($requestData["id"]) && $requestData["id"] ? Crypt::decryptString($requestData["id"]) : null;
                $isDisabled = false;
                $data = NULL;
                if ($apkMenuId) {
                    $isDisabled = true;
                    $data = ApkMenuModel::findOrFail($apkMenuId);
                }
                $throwData = [
                    "data" => $data,
                    "isDisabled" => $isDisabled
                ];
                return view('backend/apkMenu/modalFormAdd', $throwData);
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
            if (!isset($requestData["apkMenuTitle"]) || !$requestData["apkMenuTitle"]) {
                throw new Exception("Title APK Menu is Empty!", 1);
            }
            $apkMenuId = isset($requestData["apkMenuId"]) && $requestData["apkMenuId"] ? Crypt::decryptString($requestData["apkMenuId"]) : NULL;
            $title = isset($requestData["apkMenuTitle"]) && $requestData["apkMenuTitle"] ? trim($requestData["apkMenuTitle"]) : NULL;
            $order = isset($requestData["apkMenuOrder"]) && $requestData["apkMenuOrder"] ? trim($requestData["apkMenuOrder"]) : NULL;
            $status = isset($requestData["apkMenuStatus"]) && $requestData["apkMenuStatus"] ? $requestData["apkMenuStatus"] : 0;
            $dataStore = array(
                "title" => $title,
                "order" => $order,
                "status" => $status
            );
            if (!$apkMenuId) {
                $checkDataExist = ApkMenuModel::where("title", $title)->count();
                if ($checkDataExist != 0) {
                    throw new Exception("APK Menu Already Exist!", 1);
                }
            }
            ApkMenuModel::updateOrCreate(
                [
                    "id" => $apkMenuId
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
            $apkMenuId = isset($requestData["id"]) && $requestData["id"] ? Crypt::decryptString($requestData["id"]) : NULL;
            if (!$apkMenuId) {
                throw new Exception("apkMenu Id is Empty!", 1);
            }
            ApkMenuModel::findOrFail($apkMenuId)->delete();
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
}
