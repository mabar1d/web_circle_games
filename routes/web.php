<?php

use App\Http\Controllers\Backend\ApkMenuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\GameController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\NewsController;
use App\Http\Controllers\Backend\TagsController;
use App\Http\Controllers\Backend\TournamentMatchController;
use App\Http\Controllers\Backend\VideoController;
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

    //MASTER GAME
    $router->get('master/game', [GameController::class, 'index']);
    $router->get('master/game/getDatatable', [GameController::class, 'getDatatable']);
    $router->post('master/game/getFormAdd', [GameController::class, 'getFormAdd']);
    $router->post('master/game/store', [GameController::class, 'store']);
    $router->post('master/game/delete', [GameController::class, 'delete']);

    //NEWS CATEGORY
    $router->get('master/category', [CategoryController::class, 'index']);
    $router->get('master/category/getDatatable', [CategoryController::class, 'getDatatable']);
    $router->post('master/category/getFormAdd', [CategoryController::class, 'getFormAdd']);
    $router->post('master/category/store', [CategoryController::class, 'store']);
    $router->post('master/category/delete', [CategoryController::class, 'delete']);
    $router->get('master/category/getDropdown', [CategoryController::class, 'getDropdownData']);

    //APK MENU
    $router->get('master/apk_menu', [ApkMenuController::class, 'index']);
    $router->get('master/apk_menu/getDatatable', [ApkMenuController::class, 'getDatatable']);
    $router->post('master/apk_menu/getFormAdd', [ApkMenuController::class, 'getFormAdd']);
    $router->post('master/apk_menu/store', [ApkMenuController::class, 'store']);
    $router->post('master/apk_menu/delete', [ApkMenuController::class, 'delete']);

    //TAGS
    $router->get('master/tags/getDropdown', [TagsController::class, 'getDropdownData']);

    //NEWS
    $router->get('news', [NewsController::class, 'index']);
    $router->get('news/getDatatable', [NewsController::class, 'getDatatable']);
    $router->post('news/getFormAdd', [NewsController::class, 'getFormAdd']);
    $router->post('news/store', [NewsController::class, 'store']);
    $router->post('news/delete', [NewsController::class, 'delete']);

    //MASTER GAME
    $router->get('video', [VideoController::class, 'index']);
    $router->get('video/getDatatable', [VideoController::class, 'getDatatable']);
    $router->post('video/getFormAdd', [VideoController::class, 'getFormAdd']);
    $router->post('video/store', [VideoController::class, 'store']);
    $router->post('video/delete', [VideoController::class, 'delete']);

    //TOURNAMENT
    $router->get('tournament', [TournamentController::class, 'index']);
    $router->get('tournament/getDatatable', [TournamentController::class, 'getDatatable']);
    $router->post('tournament/getFormAdd', [TournamentController::class, 'getFormAdd']);
    $router->post('tournament/store', [TournamentController::class, 'store']);
    $router->post('tournament/delete', [TournamentController::class, 'delete']);
    $router->get('tournament/getDropdown', [TournamentController::class, 'getDropdownData']);
    $router->post('tournament/getInfo', [TournamentController::class, 'getInfo']);
    $router->post('tournament/rollRandomMatch', [TournamentController::class, 'getFormTournamentMatchRandom']);

    //TOURNAMENT MATCH
    $router->get('tournament/tree/match', [TournamentMatchController::class, 'indexTreeMatch']);
    $router->get('tournament/tree/match/getDatatable', [TournamentMatchController::class, 'getDatatableTreeMatch']);
    $router->post('tournament/tree/match/getFormAdd', [TournamentMatchController::class, 'getFormAddTreeMatch']);
    $router->post('tournament/tree/match/store', [TournamentMatchController::class, 'storeTreeMatch']);
    $router->post('tournament/tree/match/delete', [TournamentMatchController::class, 'deleteTreeMatch']);
});
