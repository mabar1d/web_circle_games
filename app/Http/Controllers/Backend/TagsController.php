<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TagsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            $getData = TagsModel::select("id", "name");
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
