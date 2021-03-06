<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\jwtController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::POST('/login',[AuthController::class, 'login'])->name('login');
Route::post('/register',[AuthController::class, 'create']);
Route::post('/data-xml', [jwtController::class, 'setDataXml']);
Route::POST('/reporte', [jwtController::class, 'makeReporte']);