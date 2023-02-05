<?php

use App\Http\Controllers\LoginController;
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

Route::get('/', function () {
    return view('home');
});

Route::get('/look_tournament', [TournamentController::class, 'lookTournament']);
Route::post('/get_tournament_match', [TournamentController::class, 'getTournamentMatch']);
Route::get('/login', [LoginController::class, 'index']);
