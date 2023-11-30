<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\Plano;
use App\Models\Cliente;
use App\Models\PontoLub;

use App\Models\EquipMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanoLubController extends Controller
{
    public function area()
    {
        $area = [
            [
                'id' => 1,
                'name' => 'Area 1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'Area 2',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($area);
    }

    public function subarea()
    {
        $area = [
            [
                'id' => 1,
                'name' => 'Sub Area 1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'Sub Area 2',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($area);
    }

    public function linha()
    {
        $area = [
            [
                'id' => 1,
                'name' => 'Linha 1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'Linha 2',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($area);
    }
    public function tag()
    {
        $area = [
            [
                'id' => 1,
                'name' => 'Tag 1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'Tag 2',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($area);
    }
    public function maquina()
    {
        $area = [
            [
                'id' => 1,
                'name' => 'Maquina 1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'Maquina 2',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($area);
    }
    public function conjunto()
    {
        $area = [
            [
                'id' => 1,
                'name' => 'Conjunto 1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'Conjunto 2',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($area);
    }

    public function equipamento()
    {
        $area = [
            [
                'id' => 1,
                'name' => 'Equipamento 1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'Equipamento 2',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($area);
    }

    public function store(Request $request)
    {
        $data = $request->data;

        foreach ($data as $planoData) {
            $lastPlano = Plano::orderBy('numero_plano', 'desc')->first(); // Encontre o último plano
            $nextNumeroPlano = $lastPlano ? (int) $lastPlano->numero_plano + 1 : 1; // Adicione 1 ao último número do plano ou comece com 1 se não houver planos existentes
            $existingPlano = Plano::where('codigo_mobile', $planoData['codigo_mobile'])->first();
            $cliente = Cliente::where('razao_social', $planoData['cliente'])->first();
            if (!$existingPlano) {
                $plano = Plano::create([
                    'codigo_empresa' => '0001',
                    'numero_plano' => '000' . str_pad($nextNumeroPlano, 3, '0', STR_PAD_LEFT),
                    'codigo_unidade' => $cliente->codigo_cliente,
                    'versao_plano' => ' ',
                    'data_plano' => date("Y-m-d", strtotime($planoData['dataCadastro'])),
                    'data_revisao' => date("Y-m-d", strtotime($planoData['dataRevisao'])),
                    'nome_supervisor' => ' ',
                    'nome_lubrificador' => $planoData['responsavelArea'],
                    'responsavel_kluber' => $planoData['responsavelKluber'],
                    'ativo' => 'S',
                    'codigo_mobile' => $planoData['codigo_mobile'],
                ]);

                foreach ($planoData['itensPlano'] as $itemPlanoData) {

                    $equipamento = EquipMaster::create([
                        'codigo_empresa' => '0001',
                        'planta' => $cliente->codigo_cliente,
                        'area' => $itemPlanoData['area'],
                        'subarea' => $itemPlanoData['subarea'],
                        'linha' => $itemPlanoData['linha'],
                        'tag' => $itemPlanoData['tag'],
                        'maquina' => $itemPlanoData['maquina'],
                        'conjunto' => $itemPlanoData['conjunto'],
                        'equipamento' => $itemPlanoData['equipamento'],
                        'numero_plano' => $planoData['numero_plano'],
                        'qtde_pontos_total' => 0,
                        'codigo_mobile' => $planoData['codigo_mobile'],
                        'ativo' => 'S',
                    ]);

                    foreach ($itemPlanoData['detalhes'] as $detalheData) {

                        $ponto = PontoLub::create([
                            'codigo_empresa' => '0001',
                            'id_equipamento' => $equipamento->id,
                            'numero_ponto' => $detalheData['qtyPontos'], // Ajuste conforme necessário
                            'componente' => $detalheData['componente'],
                            'numero_plano' => $planoData['numero_plano'], // Ajuste conforme necessário
                            'qtde_pontos' => $detalheData['qtyPontos'],
                            'condicao_operacional' => $detalheData['condiop'],
                            'descritivo_simplificado' => $detalheData['atividadeBreve'],
                            'descritivo_longo' => '', // Ajuste conforme necessário
                            'frequencia' => $detalheData['frequencia'],
                            'tempo_atividade' => $detalheData['tempoAtividade'],
                            'qtde_pessoas' => $detalheData['qtyPessoas'],
                            'qtde_material' => $detalheData['qtyMaterial'],
                            'unidade_medida' => $detalheData['uniMedida'],
                            'material' => $detalheData['material'],
                            'codigo_produto' => $detalheData['codigo'],
                            'nsf' => $detalheData['nsf'],
                            'data_ultimo_lancamento' => $planoData['codigo_mobile'], // Ajuste conforme necessário
                            'codigo_mobile' => $planoData['codigo_mobile'],
                            'ativo' => 'S',
                        ]);
                    }
                }
            }
        }

        return response()->json('Plano de lubrificação cadastrado com sucesso!');
    }

    public function getPlans()
    {
        $planos = Plano::with('cliente')->limit(100)->get();

        return response()->json($planos);
    }
}
