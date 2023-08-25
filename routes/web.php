<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\GameController;
use App\Http\Controllers\Backend\NewsCategoryController;
use App\Http\Controllers\Backend\NewsController;
use Illuminate\Support\Facades\Redirect;
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

Auth::routes();

Route::get('/', [HomeController::class, 'index']);
Route::get('logout', function () {
    auth()->logout();
    Session()->flush();

    return Redirect::to('/');
})->name('logout');

Route::get('/look_tournament', [TournamentController::class, 'lookTournament']);
Route::post('/get_tournament_tree_match', [TournamentController::class, 'getTournamentTreeMatch']);

$router->group(['prefix' => 'callbackMidtrans'], function ($router) {
    $router->get('finish', [MidtransController::class, 'finishPayment']);
    $router->get('unfinish', [MidtransController::class, 'unfinishPayment']);
    $router->get('error', [MidtransController::class, 'errorPayment']);
});

$router->group(['prefix' => 'be', 'middleware' => 'auth'], function ($router) {
    $router->get('dashboard', [DashboardController::class, 'index']);
    //NEWS CATEGORY
    $router->get('master/news_category', [NewsCategoryController::class, 'index']);
    $router->get('master/news_category/getDatatable', [NewsCategoryController::class, 'getDatatable']);
    $router->get('master/news_category/formModal', [NewsCategoryController::class, 'form_modal']);
    $router->get('master/news_category/store', [NewsCategoryController::class, 'store']);
    $router->get('master/news_category/delete', [NewsCategoryController::class, 'delete']);

    //MASTER GAME
    $router->get('master/game', [GameController::class, 'index']);
    $router->get('master/game/getDatatable', [GameController::class, 'getDatatable']);
    $router->post('master/game/getFormAdd', [GameController::class, 'getFormAdd']);
    $router->post('master/game/store', [GameController::class, 'store']);
    $router->post('master/game/delete', [GameController::class, 'delete']);

    //NEWS
    $router->get('news', [NewsController::class, 'index']);
    $router->get('news/getDatatable', [NewsController::class, 'getDatatable']);
    $router->post('news/getFormAdd', [NewsController::class, 'getFormAdd']);
    $router->post('news/store', [NewsController::class, 'store']);
    $router->post('news/delete', [NewsController::class, 'delete']);
});

$router->group(['prefix' => 'master', 'middleware' => 'auth'], function ($router) {
});
