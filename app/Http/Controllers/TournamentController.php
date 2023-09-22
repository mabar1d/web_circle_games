<?php

namespace App\Http\Controllers;

use App\Helpers\ApiCircleGamesHelper;
use App\Models\TournamentModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TournamentController extends BaseController
{

    public function index()
    {
        return view('tournament/index');
    }

    public function listDatatable(Request $request)
    {
        $requestData = $request->input();
        $search = isset($requestData["search"]) && $requestData["search"] ? $requestData["search"] : NULL;
        $page = isset($requestData["page"]) && $requestData["page"] ? $requestData["page"] : 1;
        $url = env('API_CIRCLE') . 'getListMyTournament';
        $user = session()->get('user');
        $requestBody['user_id'] = trim($user->id);
        $requestBody['filter_game'] = "[]";
        $requestBody['search'] = trim($search);
        $requestBody['page'] = $page;
        $requestBody['type'] = "";

        $response = ApiCircleHelpers::sendApi($url, "POST", $requestBody);
        $listData = array();
        if ($response["code"] == "00") {
            foreach ($response["data"] as $rowData) {
                $listData[] = $rowData;
            }
            $response["html"] = view('tournament.table', [
                "listData" => $listData
            ])->render();
            if (count($response["data"]) > 0) {
                $response["nextPage"] = $page + 1;
                $response["prevPage"] = $page - 1;
            } else {
                $response["nextPage"] = $page;
                $response["prevPage"] = $page - 1;
            }
        }
        return json_encode($response);
    }

    public function addNews()
    {
        $url = env('API_CIRCLE') . 'getListNewsCategory';
        $user = session()->get('user');
        $requestBody['user_id'] = $user->id;
        $requestBody['page'] = 1;


        $response = ApiCircleHelpers::sendApi($url, "POST", $requestBody);
        $listData = new stdClass;

        if ($response['code'] != "00") {
            return view('tournament.addNews')->with('error', $response['desc']);
        }

        return view('tournament/addNews', ["list_category" => $response['data']]);
    }

    public function detailNews(Request $request)
    {
        $id_tournament = $request->id_tournament;
        $url = env('API_CIRCLE') . 'getInfoNews';
        $user = session()->get('user');
        // $requestBody['user_id'] = $user->id;
        $requestBody['slug'] = $request->slug;

        $response = ApiCircleHelpers::sendApi($url, "POST", $requestBody);
        $listData = new stdClass;

        if ($response['code'] != "00") {
            return redirect()->back()->with('error', $response['desc']);
        }

        if ($response["code"] == "00") {
            $responseData = $response['data'];
            $listData->title = $responseData['title'];
            $listData->content =  $responseData['content'];
            $listData->image =  $responseData['image'];
            $listData->linkShare =  $responseData['linkShare'];
        }

        return view('tournament.detailNews', ["data" => $listData]);
    }

    public function addNewsNew(Request $request)
    {
        $requestData = $request->input();
        $image = $request->file('file');
        $url = env('API_CIRCLE') . 'createNews';
        $user = session()->get('user');
        $requestBody['user_id'] = $user->id;
        $requestBody['tournament_category_id'] = $request->tournament_category;
        $requestBody['title'] = $request->title;
        $requestBody['content'] = $request->content;
        $requestBody['image'] = $image;
        $requestBody['status'] = 1;

        $response = ApiCircleHelpers::sendApi($url, "POST", $requestBody);

        if ($response['code'] != "00") {
            return redirect('addNews')->with('error', $response['desc']);
        }

        return redirect('tournament/index')->with('success', $response['desc']);
    }

    public function lookTournament(Request $request)
    {
        $requestData = $request->input();
        $tournamentID = $requestData["tournament_id"];
        return view(
            'public.tournamentTree',
            [
                'tournament_id' => $tournamentID
            ]
        );
    }

    public function getTournamentTreeMatch(Request $request)
    {
        $requestData = $request->input();
        $tournamentID = $requestData["tournament_id"];

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $paramsBody = [
            'user_id' => '4',
            'tournament_id' => $tournamentID,
            'phase' => ''
        ];

        $responseApi = ApiCircleGamesHelper::sendRequestApi("POST", "getListMatchTournamentTree", $headers, $paramsBody);
        // dd($responseApi);
        // $responseApi2 = '{"code":"00","desc":"Success Get List Tree Tournament Match.","data":{"results":[[[[1,2],[2,1]],[[1,0],[0,1]]]],"teams":[[3,5],[7,8]]}}';
        // dd($responseApi, $responseApi2);
        // //contoh Single Elimination
        // $data['teams'] = [
        //     ["Team 1", "Team 17"],
        //     ["Team 2", "Team 18"],
        //     ["Team 3", "Team 19"],
        //     ["Team 4", "Team 20"],
        //     ["Team 5", "Team 21"],
        //     ["Team 6", "Team 22"],
        //     ["Team 7", "Team 23"],
        //     ["Team 8", "Team 24"],
        //     ["Team 9", "Team 25"],
        //     ["Team 10", "Team 26"],
        //     ["Team 11", "Team 27"],
        //     ["Team 12", "Team 28"],
        //     ["Team 13", "Team 29"],
        //     ["Team 14", "Team 30"],
        //     ["Team 15", "Team 31"],
        //     ["Team 16", "Team 32"]
        // ];
        // $data['score'] = [
        //     [
        //         [ //first leg
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //             [1, 0],
        //         ],
        //         [ //second leg
        //             [1, 3],
        //             [1, 2],
        //             [4, 1],
        //             [1, 3],
        //             [1, 4],
        //             [1, 2],
        //             [1, 2],
        //             [1, 4]
        //         ],
        //         [ //quarter Final
        //             [2, 3],
        //             [1, 2],
        //             [2, 1],
        //             [4, 3]
        //         ],
        //         [ //semi Final
        //             [2, 3],
        //             [1, 2]
        //         ],
        //         [ //Final
        //             [2, 1], //juara 1 dan 2
        //             [2, 1] //juara 3 dan 4
        //         ]
        //     ]
        // ];

        // // contoh double elimination
        // $data['teams'] = [
        //     ["Team 1", "Team 2"],
        //     ["Team 3", "Team 4"]
        // ];
        // $data['score'] = [ // List of brackets (three since this is double elimination)
        //     [ // Winner bracket
        //         [
        //             [1, 2],
        //             [3, 4]
        //         ], // First round and results
        //         [
        //             [5, 6]
        //         ] // Second round
        //     ],
        //     [ // Loser bracket
        //         [
        //             [7, 8]
        //         ], // First round
        //         [
        //             [9, 10]
        //         ] // Second round
        //     ],
        //     [ // Final "bracket"
        //         [ // First round
        //             [11, 12], // Match to determine 1st and 2nd
        //             [13, 14] // Match to determine 3rd and 4th
        //         ],
        //         [ // Second round
        //             [15, 16] // LB winner won first round (11-12) so need a final decisive round
        //         ]
        //     ]
        // ];

        // dd($response);
        // foreach ($getTeamMatch as $rowTeamMatch) {
        //     $data["teams"][] = array(
        //         $rowTeamMatch["home_team_id"],
        //         $rowTeamMatch["opponent_team_id"],
        //     );
        // }
        // $getTournamentScore = MatchTournamentModel::select("tournament_phase", "round", "home_team_id", "opponent_team_id", "score_home", "score_opponent")
        //     ->where("tournament_id", $tournamentID)
        //     ->orderBy("tournament_phase", "ASC")
        //     ->orderBy("round", "ASC")
        //     ->get()->toArray();
        // $scoreTournament = array();
        // foreach ($getTournamentScore as $rowTournamentScore) {
        //     $scoreTournament[$rowTournamentScore["tournament_phase"]][] = array(
        //         (int) $rowTournamentScore["score_home"],
        //         (int) $rowTournamentScore["score_opponent"]
        //     );
        // }
        // $scoreTournament = array_values($scoreTournament);
        // $data["score"] = $scoreTournament;
        return $responseApi;
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
            $getData = TournamentModel::select("id", "name");
            if (isset($requestData["typeTournament"]) && $requestData["typeTournament"]) {
                $getData = $getData->where('type', $requestData["typeTournament"]);
            }
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

    public function getInfo(Request $request)
    {
        if (request()->ajax()) {
            $requestData = $request->input();
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];

            $paramsBody = [
                'user_id' => 'web',
                'tournament_id' => isset($requestData["tournament_id"]) && $requestData["tournament_id"] ? $requestData["tournament_id"] : NULL
            ];
            $getApiResponse = ApiCircleGamesHelper::sendRequestApi("POST", "getInfoTournament", $headers, $paramsBody);
            $result = json_decode($getApiResponse, true);
            return $result;
        }
    }

    public function getFormTournamentMatchRandom(Request $request)
    {
        $response = [
            "code" => 1,
            "desc" => "Error"
        ];
        try {
            $requestData = $request->input();
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];

            $paramsBody = [
                'user_id' => 'web',
                'tournament_id' => isset($requestData["tournament_id"]) && $requestData["tournament_id"] ? $requestData["tournament_id"] : NULL
            ];
            $getApiResponse = ApiCircleGamesHelper::sendRequestApi("POST", "randomMatchTournamentTree", $headers, $paramsBody);
            $resultApi = json_decode($getApiResponse, true);
            if ($resultApi["code"] != "00") {
                throw new Exception($resultApi["desc"], $resultApi["code"]);
            }
            return view('backend/tournamentMatch/formTournamentMatchRandom', [
                "tournamentId" => isset($requestData["tournament_id"]) && $requestData["tournament_id"] ? $requestData["tournament_id"] : NULL,
                "data" => isset($resultApi["data"]) && $resultApi["data"] ? $resultApi["data"] : NULL
            ]);
        } catch (Exception $e) {
            $response["code"] = $e->getCode();
            $response["desc"] = $e->getMessage();
            return $response;
        }
    }
}
