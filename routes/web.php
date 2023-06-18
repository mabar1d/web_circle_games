<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/look_tournament', [TournamentController::class, 'lookTournament']);
Route::post('/get_tournament_tree_match', [TournamentController::class, 'getTournamentTreeMatch']);

$router->group(['prefix' => 'callbackMidtrans'], function ($router) {
    $router->get('finish', [MidtransController::class, 'finishPayment']);
    $router->get('unfinish', [MidtransController::class, 'unfinishPayment']);
    $router->get('error', [MidtransController::class, 'errorPayment']);
});
