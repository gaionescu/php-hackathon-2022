<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProgrammeController;
use App\Http\Controllers\API\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::post('logout',[AuthController::class,'logout'])->middleware('auth:api');


Route::get('showprogrammes',[ProgrammeController::class,'index']);
Route::get('viewprogramme',[ProgrammeController::class,'show']);
Route::post('deleteprogramme',[ProgrammeController::class,'destroy'])->middleware('auth:api');
Route::post('addprogramme',[ProgrammeController::class,'store'])->middleware('auth:api');

Route::post('participa',[UserController::class,'participa'])->middleware('auth:api');
Route::post('anuleazaParticipare',[UserController::class,'anuleazaParticipare'])->middleware('auth:api');

Route::apiResource('programmes',ProgrammeController::class);
