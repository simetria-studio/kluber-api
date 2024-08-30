<?php

namespace App\Jobs;

use App\Models\Plano;
use App\Models\Area;
use App\Models\SubArea;
use App\Models\Linha;
use App\Models\Maquina;
use App\Models\EquipMaster;
use App\Models\AtvComponente;
use App\Models\TextoLongo;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPlanoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $planoData;

    public function __construct($planoData)
    {
        $this->planoData = $planoData;
    }

    public function handle()
    {

        try {
            $planoData = $this->planoData;
            $lastPlano = Plano::orderBy('numero_plano', 'desc')->first();
            $nextNumeroPlano = $lastPlano ? (int) $lastPlano->numero_plano + 1 : 1;
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

                foreach ($planoData['areas'] as $area) {
                    $novaArea = new Area();
                    $novaArea->nome_area = $area['nome'];
                    $novaArea->id_plano = $plano->id;
                    $novaArea->ativo = 'S';
                    $novaArea->save();

                    foreach ($area['subareas'] as $subarea) {
                        $novaSubarea = new SubArea();
                        $novaSubarea->nome_subarea = $subarea['nome'];
                        $novaSubarea->id_area = $novaArea->id;
                        $novaSubarea->ativo = 'S';
                        $novaSubarea->save();

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

                                foreach ($tagMaquina['conjuntos_equip'] as $conjuntoEquip) {
                                    $novoConjuntoEquip = new EquipMaster();
                                    $novoConjuntoEquip->codigo_empresa = '0001';
                                    $novoConjuntoEquip->planta = $plano->codigo_unidade;
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

                                    $sequencial = 1;
                                    foreach ($conjuntoEquip['pontos'] as $ponto) {
                                        //ELLER NÃO MEXER NESSE CÓDIGO, SE PODE CAGAR COM TUDO 
                                        $textoLongo = TextoLongo::where('codigo_atv', $ponto['atv_breve_codigo'])->first();

                                        if ($textoLongo) {
                                           
                                            $textoComSubstituicao = str_replace('{{DESCRIÇÃO_MATERIAL}}', $ponto['lub_name'], $textoLongo->texto);
                                           
                                            $textoComSubstituicao = str_replace('{{CODIGO_PRODUTO}}', $ponto['lub_codigo'], $textoComSubstituicao);
                                        } else {
                                            $textoComSubstituicao = '';
                                        }

                                        \Log::info('Texto longo após substituições: ' . $textoComSubstituicao);

                                        AtvComponente::create([
                                            'codigo_empresa' => '0001',
                                            'id_equipamento' => $novoConjuntoEquip->id,
                                            'componente' => $ponto['component_codigo'],
                                            'numero_plano' => $plano->numero_plano,
                                            'numero_ponto' => $sequencial,
                                            'qtde_pontos' => $ponto['qty_pontos'] ?? ' ',
                                            'condicao_operacional' => $ponto['cond_op_codigo'] ?? ' ',
                                            'descritivo_simplificado' => $ponto['atv_breve_name'] ?? ' ',
                                            'descritivo_longo' => $ponto['atv_breve_name'] ?? ' ',
                                            'frequencia' => $ponto['period_codigo'] ?? ' ',
                                            'tempo_atividade' => $ponto['tempo_atv'] ?? ' ',
                                            'qtde_pessoas' => $ponto['qty_pessoas'] ?? ' ',
                                            'qtde_material' => $ponto['qty_material'] ?? ' ',
                                            'unidade_medida' => $ponto['unidade_medida_codigo'] ?? ' ',
                                            'material' => $ponto['lub_codigo'] ?? ' ',
                                            'codigo_produto' => ' ',
                                            'nsf' => '  ',
                                            'data_ultimo_lancamento' => Carbon::now(),
                                            'texto_longo' => $textoComSubstituicao,
                                            'ativo' => 'S',
                                        ]);

                                        $sequencial++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            throw $e; 
        }
    }
}
