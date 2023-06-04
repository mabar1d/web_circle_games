<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use stdClass;

class MidtransController extends BaseController
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
        dd($requestData);
        return view('public.midtrans.finish_payment', []);
    }
}
