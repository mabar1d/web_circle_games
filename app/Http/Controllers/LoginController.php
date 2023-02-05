<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class LoginController extends BaseController{
    public function index(){
        return view('login');
    }
}