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

class TournamentMatchController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.tournamentMatch.index', []);
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
                'page' => 1,
                'filter_game' => NULL
            ];
            $getListNewsApi = ApiCircleGamesHelper::sendRequestApi("POST", "getListTournament", $headers, $paramsBody);
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
                    $result .= '<button type="button" class="btn btn-warning btn-xs m-1" data-id="' . $row["id"] . '">View</button>';
                    $result .= '<button type="button" class="btn btn-danger btn-xs m-1" data-id="' . $row["id"] . '">Delete</button>';
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
            "desc" => ""
        );
        try {
            $requestData = $request->input();
            if (!isset($requestData["tournament_id"]) || !$requestData["tournament_id"]) {
                throw new Exception("Tournament ID is Empty!", 1);
            }
            $tournamentId = isset($requestData["tournament_id"]) && $requestData["tournament_id"] ? $requestData["tournament_id"] : NULL;

            if (!isset($requestData["match_array"]) || !$requestData["match_array"]) {
                throw new Exception("Data Tournament Match is Empty!", 1);
            }
            $matchArray = isset($requestData["match_array"]) && $requestData["match_array"] ? json_encode($requestData["match_array"]) : NULL;

            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];

            $paramsBody = [
                'user_id' => 'web',
                'tournament_id' => $tournamentId,
                'match_array' => $matchArray
            ];
            $getResponseApi = ApiCircleGamesHelper::sendRequestApi("POST", "setMatchTournamentTree", $headers, $paramsBody);
            $response = json_decode($getResponseApi, true);
        } catch (Exception $e) {
            $response = array(
                "code" => $e->getCode(),
                "desc" => $e->getMessage()
            );
        }
        return json_encode($response);
    }
}
