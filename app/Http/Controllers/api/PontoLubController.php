<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ProdutoLub;
use App\Models\UnidadeMed;
use Illuminate\Http\Request;

class PontoLubController extends Controller
{
    public function components(Request $request)
    {

        if (!$request->search_text) {
            $components = Component::where('nome', 'componente')->get();
            return response()->json($components);
        } else {
            $components = Component::where('nome', 'componente')->where('descricao', 'like', '%' . $request->search_text . '%')->get();
            return response()->json($components);
        }
    }

    public function condOp(Request $request)
    {

        if (!$request->search_text) {
            $condOp = Component::where('nome', 'condicao_operacional')->get();
            return response()->json($condOp);
        } else {
            $condOp = Component::where('nome', 'condicao_operacional')->where('descricao', 'like', '%' . $request->search_text . '%')->get();
            return response()->json($condOp);
        }
    }

    public function atividadeBreve(Request $request)
    {

        if (!$request->search_text) {
            $atividadeBreve = Component::where('nome', 'descricao_atividade')->get();
            return response()->json($atividadeBreve);
        } else {
            $atividadeBreve = Component::where('nome', 'descricao_atividade')->where('descricao', 'like', '%' . $request->search_text . '%')->get();
            return response()->json($atividadeBreve);
        }
    }

    public function unidadeMed()
    {

        $unidadeMed = UnidadeMed::all();

        return response()->json($unidadeMed);
    }

    public function frequencia(Request $request)
    {

        if (!$request->search_text) {
            $frequencia = Component::where('nome', 'frequencia')->get();
            return response()->json($frequencia);
        } else {
            $frequencia = Component::where('nome', 'frequencia')->where('descricao', 'like', '%' . $request->search_text . '%')->get();
            return response()->json($frequencia);
        }

    }

    public function material(Request $request)
    {

        if (!$request->search_text) {
            $material = ProdutoLub::all();
            return response()->json($material);
        } else {
            $material = ProdutoLub::where('descricao_produto', 'like', '%' . $request->search_text . '%')->get();
            return response()->json($material);
        }
    }

    public function nsf()
    {
        $nsf = Component::where('nome', 'nsf')->get();

        return response()->json($nsf);
    }
}
