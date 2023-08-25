<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\GameModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GameController extends Controller
{
    public function __construct()
    {
        session(['menu' => 'master_game']);
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.game.index', []);
    }

    public function getDatatable(Request $request)
    {
        if (request()->ajax()) {
            $requestData = $request->input();
            $listGame = GameModel::select(
                'm_game_uid',
                'title',
                'desc',
                'image',
                'status'
            );
            return Datatables::of($listGame)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $result = isset($row['status']) && $row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
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
                return view('backend/game/modalFormAdd');
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
            if (!isset($requestData["gameTitle"]) || !$requestData["gameTitle"]) {
                throw new Exception("Title Game is Empty!", 1);
            }
            $title = isset($requestData["gameTitle"]) && $requestData["gameTitle"] ? trim(strtolower($requestData["gameTitle"])) : NULL;
            $desc = isset($requestData["gameDesc"]) && $requestData["gameDesc"] ? trim($requestData["gameDesc"]) : NULL;
            $image = isset($requestData["gameImage"]) && $requestData["gameImage"] ? trim($requestData["gameImage"]) : NULL;
            $status = isset($requestData["gameStatus"]) && $requestData["gameStatus"] ? $requestData["gameStatus"] : 0;
            $dataStore = array(
                "title" => $title,
                "desc" => $desc,
                "image" => $image,
                "status" => $status
            );
            $checkDataExist = GameModel::where("title", $title)->count();
            if ($checkDataExist != 0) {
                throw new Exception("Game Already Exist!");
            }
            GameModel::create($dataStore);
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
