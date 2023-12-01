<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ProdutoLub;
use App\Models\UnidadeMed;
use Illuminate\Http\Request;

class PontoLubController extends Controller
{
    public function components()
    {

        $components = Component::where('nome', 'componente')->get();

        return response()->json($components);
    }

    public function condOp()
    {

        $condOp = Component::where('nome', 'condicao_operacional')->get();

        return response()->json($condOp);
    }

    public function atividadeBreve()
    {

        $atividadeBreve = Component::where('nome', 'ativade_breve')->get();

        return response()->json($atividadeBreve);
    }

    public function unidadeMed()
    {

        $unidadeMed = UnidadeMed::all();

        return response()->json($unidadeMed);
    }

    public function frequencia()
    {

        $frequencia = Component::where('nome', 'frequencia')->get();
        return response()->json($frequencia);
    }

    public function material()
    {

        $material = ProdutoLub::all();

        return response()->json($material);
    }

    public function nsf()
    {
        $nsf = Component::where('nome', 'nsf')->get();

        return response()->json($nsf);
    }
}
