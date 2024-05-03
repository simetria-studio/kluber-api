<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\Linha;
use App\Models\Plano;

use App\Models\Cliente;
use App\Models\SubArea;
use App\Models\PontoLub;
use App\Models\EquipMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AtvComponente;
use App\Models\Maquina;

class PlanoLubController extends Controller
{
   

    public function store(Request $request)
    {
        try {
            $data = $request->all();
    
            \Log::info($data);
    
            foreach ($data as $planoData) {
                // Salvar o plano de lubrificação
                $lastPlano = Plano::orderBy('numero_plano', 'desc')->first(); // Encontre o último plano
                $nextNumeroPlano = $lastPlano ? (int) $lastPlano->numero_plano + 1 : 1; // Adicione 1 ao último número do plano ou comece com 1 se não houver planos existentes
                $existingPlano = Plano::where('codigo_mobile', $planoData['codigo_mobile'])->first();
                $cliente = Cliente::where('razao_social', $planoData['cliente'])->first();
                if (!$existingPlano) {
                    $plano = Plano::create([
                        'codigo_empresa' => '0001',
                        'numero_plano' => '000' . str_pad($nextNumeroPlano, 3, '0', STR_PAD_LEFT),
                        'codigo_unidade' => $cliente->codigo_cliente ?? ' ',
                        'versao_plano' => ' ',
                        'data_plano' => date("Y-m-d", strtotime($planoData['data_cadastro'])),
                        'data_revisao' => date("Y-m-d", strtotime($planoData['data_revisao'])),
                        'nome_supervisor' => ' ',
                        'nome_lubrificador' => $planoData['responsavel_lubrificacao'] ?? ' ',
                        'responsavel_kluber' => $planoData['responsavel_kluber'] ?? ' ',
                        'ativo' => 'S',
                        'codigo_mobile' => $planoData['codigo_mobile'] ?? ' ',
                    ]);
    
                    // Iterar sobre as áreas
                    foreach ($planoData['areas'] as $area) {
                        $novaArea = new Area();
                        $novaArea->nome_area = $area['nome'];
                        $novaArea->id_plano = $plano->id;
                        $novaArea->ativo = 'S';
                        $novaArea->save();
    
                        // Iterar sobre as subáreas
                        foreach ($area['subareas'] as $subarea) {
                            $novaSubarea = new SubArea();
                            $novaSubarea->nome_subarea = $subarea['nome'];
                            $novaSubarea->id_area = $novaArea->id;
                            $novaSubarea->ativo = 'S';
                            $novaSubarea->save();
    
                            // Iterar sobre as linhas
                            foreach ($subarea['linhas'] as $linha) {
                                $novaLinha = new Linha();
                                $novaLinha->nome_linha = $linha['nome'];
                                $novaLinha->id_subarea = $novaSubarea->id;
                                $novaLinha->ativo = 'S';
                                $novaLinha->save();
    
    
                                foreach ($linha['tags_maquinas'] as $tagMaquina) {
                                    $novaTagMaquina = new Maquina();
                                    $novaTagMaquina->nome_maquina = $tagMaquina['maquina_nome'] ?? ' ';
                                    $novaTagMaquina->id_linha = $novaLinha->id;
                                    $novaTagMaquina->tag = $tagMaquina['tag_nome'] ?? ' ';
                                    $novaTagMaquina->ativo = 'S';
                                    $novaTagMaquina->save();
    
                                    // Iterar sobre os conjuntos de equipamentos
                                    foreach ($tagMaquina['conjuntos_equip'] as $conjuntoEquip) {
                                        $novoConjuntoEquip = new EquipMaster();
                                        $novoConjuntoEquip->codigo_empresa = '0001';
                                        $novoConjuntoEquip->planta = '0001';
                                        $novoConjuntoEquip->area = $novaArea->id;
                                        $novoConjuntoEquip->subarea = $novaSubarea->id;
                                        $novoConjuntoEquip->linha = $novaLinha->id;
                                        $novoConjuntoEquip->maquina = $novaTagMaquina->id;
                                        $novoConjuntoEquip->conjunto = $conjuntoEquip['conj_nome'];
                                        $novoConjuntoEquip->equipamento = $conjuntoEquip['equi_nome'];
                                        $novoConjuntoEquip->numero_plano = $plano->numero_plano;
                                        $novoConjuntoEquip->qtde_pontos_total = '  ';
                                        $novoConjuntoEquip->ativo = 'S';
                                        $novoConjuntoEquip->save();
                                        // Iterar sobre os pontos
                                        foreach ($conjuntoEquip['pontos'] as $ponto) {
                                            $componente = AtvComponente::create([
                                                'componente' => $ponto['component_codigo'],
                                                'sequencia' => $plano->numero_plano,
                                                'qtde_pontos' => $ponto['qty_pontos'] ?? ' ',
                                                'condicao_operacional' => $ponto['cond_op_codigo'] ?? ' ',
                                                'descritivo_simplificado' => $ponto['atv_breve_name'] ?? ' ',
                                                'frequencia' => $ponto['period_codigo'] ?? ' ',
                                                'periodicidade' => $ponto['period_name'] ?? ' ',
                                                'tempo_atividade' => $ponto['tempo_atv'] ?? ' ',
                                                'qtde_pessoas' => $ponto['qty_pessoas'] ?? ' ',
                                                'qtde_material' => $ponto['qty_material'] ?? ' ',
                                                'codigo_produto' => $ponto['lub_codigo'] ?? ' ',
                                                'data_hora_alteracao' => Carbon::now(),
                                                'ativo' => 'S',
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
    
            return response()->json('Plano(s) de lubrificação cadastrado(s) com sucesso!');
        } catch (\Exception $e) {
            // Handle the exception here
            \Log::info($e->getMessage());
            return response()->json('Ocorreu um erro ao processar a solicitação.', 500);
        }
    }
    

    public function getPlans()
    {
        $planos = Plano::with('cliente')->get();

        return response()->json($planos);
    }


}
