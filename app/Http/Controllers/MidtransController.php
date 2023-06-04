<?php

namespace App\Http\Controllers;

use App\Helpers\ApiCircleGamesHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use stdClass;

class TournamentController extends BaseController
{

    public function index()
    {
    }

    public function finish(Request $request)
    {
        $response = new stdClass();
        $response->code = '';
        $response->desc = '';
        $requestData = $request->input();
        DB::beginTransaction();
        try {
        } catch (Exception $e) {
        }
        return view('public.midtrans.finish_payment', []);
    }
}
