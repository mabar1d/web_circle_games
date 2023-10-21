<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiCircleGamesHelper;
use App\Http\Controllers\Controller;
use App\Models\GameModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.game.index', []);
    }

    public function getDatatable(Request $request)
    {
        try {
            if (request()->ajax()) {
                $requestData = $request->input();
                $draw = $requestData['draw'];
                $search = isset($requestData['search']['value']) && $requestData['search']['value'] ? $requestData['search']['value'] : "";
                $orderBy = isset($requestData['order_by']) && $requestData['order_by'] ? $requestData['order_by'] : NULL;
                $orderByMethod = isset($requestData['order_by_method']) && $requestData['order_by_method'] ? $requestData['order_by_method'] : NULL;
                $offset = isset($requestData['start']) && $requestData['start'] ? $requestData['start'] : NULL;
                $limit = isset($requestData['length']) && $requestData['length'] ? $requestData['length'] : NULL;
                $status = isset($requestData['status']) && $requestData['status'] ? $requestData['status'] : NULL;
                $body = [
                    'user_id' => Auth::id(),
                    'search' => $search,
                    'order_by' => $orderBy,
                    'order_by_method' => $orderByMethod,
                    'limit' => $limit,
                    'offset' => $offset,
                    'status' => $status
                ];
                $listGame = ApiCircleGamesHelper::sendRequestApi('POST', 'getListMasterGame', NULL, $body);
                if ($listGame["code"] != "00") {
                    throw new Exception($listGame["message"], $listGame["code"]);
                }

                // $resultData = array();
                // foreach ($listGame["data"] as $rowData) {
                //     $resultData[] = [];
                // }

                $countListGame = ApiCircleGamesHelper::sendRequestApi('POST', 'countMasterGame', NULL, [
                    'user_id' => Auth::id(),
                    'status' => $status
                ]);
                if ($countListGame["code"] != "00") {
                    throw new Exception($countListGame["message"], $countListGame["code"]);
                }
                $countData = $countListGame["data"]["totalCount"];

                return Datatables::of($listGame["data"])
                    ->addIndexColumn()
                    ->setTotalRecords($countData)
                    ->with([
                        "draw" => (int)$draw,
                        // "data" => $listGame["data"]
                    ])
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
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getFormAdd(Request $request)
    {
        try {
            if (request()->ajax()) {
                $requestData = $request->input();
                $gameId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : null;
                $isDisabled = false;
                $data = NULL;
                if ($gameId) {
                    $isDisabled = true;
                    $data = GameModel::findOrFail($gameId);
                    $data['game_image_url'] = env('URL_API_CIRCLE_GAMES') . "upload/masterGame/" . $data["image"];
                }
                $throwData = [
                    "data" => $data,
                    "isDisabled" => $isDisabled
                ];
                return view('backend/game/modalFormAdd', $throwData);
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
            $gameId = isset($requestData["gameId"]) && $requestData["gameId"] ? $requestData["gameId"] : NULL;
            $title = isset($requestData["gameTitle"]) && $requestData["gameTitle"] ? trim($requestData["gameTitle"]) : NULL;
            $desc = isset($requestData["gameDesc"]) && $requestData["gameDesc"] ? trim($requestData["gameDesc"]) : NULL;
            $status = isset($requestData["gameStatus"]) && $requestData["gameStatus"] ? $requestData["gameStatus"] : 0;

            $dataStore = array(
                "title" => $title,
                "desc" => $desc,
                "status" => $status
            );

            //upload image to api
            if (request('gameImage')) {
                $file               = request('gameImage');
                $file_path          = $file->getPathname();
                $file_mime          = $file->getMimeType('image');
                $file_uploaded_name = $file->getClientOriginalName();
                $api_url = env('URL_API_CIRCLE_GAMES') . 'api/uploadImageGame';

                $client = new \GuzzleHttp\Client();

                $responseApi = $client->request("POST", $api_url, [
                    // jika menggunakan authorization
                    'headers' => ['Authorization' => 'Bearer ' . env('GOD_BEARER_TOKEN')],
                    'multipart' => [
                        [
                            'name' => 'image_file',
                            'filename' => $file_uploaded_name,
                            'Mime-Type' => $file_mime,
                            'contents' => fopen($file_path, 'r'),
                        ]
                    ],
                    'verify' => false
                ]);

                $responseApi   = $responseApi->getBody();
                $responseApiData = json_decode($responseApi, true);
                if ($responseApiData["code"] != '00') {
                    throw new Exception($responseApiData["desc"], 1);
                }

                $dataStore["image"] = isset($responseApiData["data"]["filename"]) && $responseApiData["data"]["filename"] ? $responseApiData["data"]["filename"] : NULL;
            }

            if (!$gameId) {
                $checkDataExist = GameModel::where("title", $title)->count();
                if ($checkDataExist != 0) {
                    throw new Exception("Game Already Exist!", 1);
                }
            }
            $insertGame = GameModel::updateOrCreate(
                [
                    "id" => $gameId
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
            $gameId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : NULL;
            if (!$gameId) {
                throw new Exception("Game Id is Empty!", 1);
            }
            GameModel::findOrFail($gameId)->delete();
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
