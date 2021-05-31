<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\MatchController;
use \App\Http\Controllers\HasMatch;
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
    return view('welcome');
});

Route::post('/createFixture',[HasMatch::class,'createFixture'])->name("createFixture");
Route::get('/',[MatchController::class,'index'])->name("home");
Route::post('/playMatch',[MatchController::class,'playMatch'])->name('playMatch');
Route::post('/getPointsTable',[MatchController::class,'getPointsTable'])->name('getPointsTable');
Route::post('/clearFixture',[MatchController::class,'clearFixture'])->name('clearFixture');
Route::post('/getMatchScore',[MatchController::class,'getMatchScore'])->name('getMatchScore');
