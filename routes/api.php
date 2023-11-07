<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ClienteController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login-app', [AuthController::class, 'loginApi']);
Route::post('/get-user-info', [UserController::class, 'getUserInfo']);
Route::post('/get-users-kluber', [UserController::class, 'getUsersKluber']);

Route::post('/get-clientes', [ClienteController::class, 'getClientes']);