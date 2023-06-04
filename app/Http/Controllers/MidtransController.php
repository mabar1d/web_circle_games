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

    public function finishPayment(Request $request)
    {
        $response = new stdClass();
        $response->code = '';
        $response->desc = '';
        $requestData = $request->input();
        return view('public.midtrans.finish_payment', []);
    }

    public function unfinishPayment(Request $request)
    {
        $response = new stdClass();
        $response->code = '';
        $response->desc = '';
        $requestData = $request->input();
        return view('public.midtrans.unfinish_payment', []);
    }

    public function errorPayment(Request $request)
    {
        $response = new stdClass();
        $response->code = '';
        $response->desc = '';
        $requestData = $request->input();
        return view('public.midtrans.error_payment', []);
    }
}
