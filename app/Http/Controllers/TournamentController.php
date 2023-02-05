<?php

namespace App\Http\Controllers;

use App\Models\MatchTournamentModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TournamentController extends BaseController
{

    public function lookTournament(Request $request)
    {
        $requestData = $request->input();
        $tournamentID = $requestData["tournament_id"];
        return view(
            'tournamentTree',
            [
                'tournament_id' => $tournamentID
            ]
        );
    }

    public function getTournamentMatch(Request $request)
    {
        $requestData = $request->input();
        $tournamentID = $requestData["tournament_id"];
        $getTeamMatch = MatchTournamentModel::select("home_team_id", "opponent_team_id")
            ->where("tournament_id", $tournamentID)
            ->where("tournament_phase", "1")
            ->orderBy("round", "ASC")
            ->get()->toArray();
        $data = array();
        foreach ($getTeamMatch as $rowTeamMatch) {
            $data["teams"][] = array(
                $rowTeamMatch["home_team_id"],
                $rowTeamMatch["opponent_team_id"],
            );
        }
        $getTournamentScore = MatchTournamentModel::select("tournament_phase", "round", "home_team_id", "opponent_team_id", "score_home", "score_opponent")
            ->where("tournament_id", $tournamentID)
            ->orderBy("tournament_phase", "ASC")
            ->orderBy("round", "ASC")
            ->get()->toArray();
        $scoreTournament = array();
        foreach ($getTournamentScore as $rowTournamentScore) {
            $scoreTournament[$rowTournamentScore["tournament_phase"]][] = array(
                (int) $rowTournamentScore["score_home"],
                (int) $rowTournamentScore["score_opponent"]
            );
        }
        $scoreTournament = array_values($scoreTournament);
        $data["score"] = $scoreTournament;
        return json_encode($data);
    }
}
