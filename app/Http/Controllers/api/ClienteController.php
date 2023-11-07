<?php

namespace App\Http\Controllers\api;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    public function getClientes(Request $request)
    {
        if (!$request->search_text) {
            $clientes = Cliente::where('ativo', 'S')->select([
                'id',
                'codigo_empresa',
                'codigo_cliente',
                'razao_social',
                'nome_fantasia',
                'email',
                'ativo',
            ])->get();
            if (!$clientes) return response()->json([
                'status' => 'error',
                'message' => 'Clientes não encontrados'
            ], 404);
            return response()->json($clientes);
        } else {
            $clientes = Cliente::where('ativo', 'S')->where('razao_social', 'like', '%' . $request->search_text . '%')->select([
                'id',
                'codigo_empresa',
                'codigo_cliente',
                'razao_social',
                'nome_fantasia',
                'email',
                'ativo',
            ])->get();
            if (!$clientes) return response()->json([
                'status' => 'error',
                'message' => 'Clientes não encontrados'
            ], 404);
            return response()->json($clientes);
        }
    }
}
