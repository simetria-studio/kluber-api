<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\api\PontoLubController;


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

Route::post('/get-components', [PontoLubController::class, 'components']);
Route::post('/get-cond-op', [PontoLubController::class, 'condOp']);
Route::post('/get-unidade-med', [PontoLubController::class, 'unidadeMed']);
Route::post('/get-atividade-breve', [PontoLubController::class, 'atividadeBreve']);
Route::post('/get-frequencia', [PontoLubController::class, 'frequencia']);
Route::post('/get-material', [PontoLubController::class, 'material']);
Route::post('/get-nsf', [PontoLubController::class, 'nsf']);