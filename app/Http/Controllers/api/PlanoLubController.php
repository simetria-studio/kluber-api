<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\Linha;
use App\Models\Plano;

use App\Models\Cliente;
use App\Models\Maquina;
use App\Models\SubArea;
use App\Models\PontoLub;
use App\Models\EquipMaster;
use Illuminate\Http\Request;
use App\Jobs\ProcessPlanoJob;
use App\Models\AtvComponente;
use App\Http\Controllers\Controller;

class PlanoLubController extends Controller
{


    public function store(Request $request)
    {
        \Log::info(['store' => $request->all()]);
        $data = $request->all();

        \Log::info('Plano de lubrificação recebido: ' . json_encode($data));
        foreach ($data as $planoData) {
            // Dispara o job para processamento assíncrono
            ProcessPlanoJob::dispatch($planoData);
        }

        return response()->json('Plano(s) de lubrificação sendo processado(s)!');
    }


    public function getPlans()
    {
        $planos = Plano::with('cliente')->get();

        return response()->json($planos);
    }
}
