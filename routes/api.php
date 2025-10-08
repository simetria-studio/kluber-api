<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\api\MyPressController;
use App\Http\Controllers\api\PlanoLubController;
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

Route::post('/get-area', [PlanoLubController::class, 'area']);
Route::post('/get-subarea', [PlanoLubController::class, 'subarea']);
Route::post('/get-linha', [PlanoLubController::class, 'linha']);
Route::post('/get-tag', [PlanoLubController::class, 'tag']);
Route::post('/get-maquina', [PlanoLubController::class, 'maquina']);
Route::post('/get-conjunto', [PlanoLubController::class, 'conjunto']);
Route::post('/get-equipamento', [PlanoLubController::class, 'equipamento']);
Route::post('/sync-plan', [PlanoLubController::class, 'store']);
Route::post('/get-plan', [PlanoLubController::class, 'getPlans']);

Route::post('/my-press', [MyPressController::class, 'save']);
Route::post('/my-press-create', [MyPressController::class, 'create']);

Route::post('/new-user', [UserController::class, 'newUser']);



Route::post('atualiza-senha', [AuthController::class, 'atualizaSenha'])->name('post.password.atualiza');
// Route::get('/get-my-press', [MyPressController::class, 'index']);
// Route::get('mypress/status/{jobId}', [MyPressController::class, 'checkStatus']);
