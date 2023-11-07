<?php

namespace App\Http\Controllers\api;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUserInfo(Request $request)
    {
        $user = Usuario::where('access_token', $request->access_token)->first();
        if (!$user) return response()->json([
            'status' => 'error',
            'message' => 'Usuário não encontrado'
        ], 404);

        return response()->json($user);
    }

    public function getUsersKluber(Request $request)
    {
        if (!$request->search_text) {
            $users = Usuario::where('nivel_kluber', 'COL')->select([
                'id',
                'nome_usuario',
                'nome_usuario_completo',
            ])->get();

            return response()->json($users);
        } else {
            $users = Usuario::where('nivel_kluber', 'COL')->where('nome_usuario_completo', 'like', '%' . $request->search_text . '%')->select([
                'id',
                'nome_usuario',
                'nome_usuario_completo',
            ])->get();

            return response()->json($users);
        }
    }
}
