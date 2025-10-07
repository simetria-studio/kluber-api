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
use Illuminate\Support\Facades\Log;

class ProcessPlanoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $planoData;

    /**
     * O número de tentativas do job.
     */
    public $tries = 3;

    /**
     * O número de segundos que o job pode ser executado antes de timeout.
     */
    public $timeout = 300; // 5 minutos

    /**
     * O número de segundos para aguardar antes de tentar novamente.
     */
    public $backoff = [30, 60, 120]; // Backoff exponencial

    public function __construct($planoData)
    {
        $this->planoData = $planoData;
    }

    public function handle()
    {
        // Aumentar limite de memória para arquivos grandes
        ini_set('memory_limit', '512M');

        // Aumentar tempo limite de execução
        set_time_limit(300);

        try {
            $planoData = $this->planoData;

            // Log apenas informações essenciais para evitar problemas de memória
            Log::info('Iniciando processamento do plano', [
                'codigo_mobile' => $planoData['codigo_mobile'] ?? 'N/A',
                'cliente' => $planoData['cliente'] ?? 'N/A',
                'total_areas' => count($planoData['areas'] ?? [])
            ]);

            $lastPlano = Plano::orderBy('numero_plano', 'desc')->first();
            $nextNumeroPlano = $lastPlano ? (int) $lastPlano->numero_plano + 1 : 1;
            $existingPlano = Plano::where('codigo_mobile', $planoData['codigo_mobile'])->first();
            $cliente = Cliente::where('razao_social', $planoData['cliente'])->first();
            if (!$existingPlano) {
                Log::info('Criando novo plano', ['numero_plano' => $nextNumeroPlano]);
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

                $totalAreas = count($planoData['areas']);
                Log::info("Processando {$totalAreas} áreas");

                foreach ($planoData['areas'] as $index => $area) {
                    Log::info("Processando área " . ($index + 1) . "/{$totalAreas}: " . ($area['nome'] ?? 'N/A'));
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
                                    $totalPontos = count($conjuntoEquip['pontos']);
                                    Log::info("Processando {$totalPontos} pontos para conjunto {$conjuntoEquip['conj_nome']}");

                                    foreach ($conjuntoEquip['pontos'] as $pontoIndex => $ponto) {
                                        //ELLER NÃO MEXER NESSE CÓDIGO, SE PODE CAGAR COM TUDO
                                        $textoLongo = TextoLongo::where('codigo_atv', $ponto['atv_breve_codigo'])->first();

                                        if ($textoLongo) {
                                            $textoComSubstituicao = str_replace('{{DESCRIÇÃO_MATERIAL}}', $ponto['lub_name'], $textoLongo->texto);
                                            $textoComSubstituicao = str_replace('{{CODIGO_PRODUTO}}', $ponto['lub_codigo'], $textoComSubstituicao);
                                        } else {
                                            $textoComSubstituicao = '';
                                        }

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

                                        // Log de progresso a cada 10 pontos para arquivos grandes
                                        if (($pontoIndex + 1) % 10 === 0) {
                                            Log::info("Processados " . ($pontoIndex + 1) . "/{$totalPontos} pontos");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                Log::info('Plano processado com sucesso', [
                    'numero_plano' => $plano->numero_plano,
                    'total_areas' => $totalAreas
                ]);
            } else {
                Log::info('Plano já existe, pulando processamento', [
                    'codigo_mobile' => $planoData['codigo_mobile']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar plano', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'codigo_mobile' => $planoData['codigo_mobile'] ?? 'N/A'
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Job ProcessPlanoJob falhou definitivamente', [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'codigo_mobile' => $this->planoData['codigo_mobile'] ?? 'N/A',
            'cliente' => $this->planoData['cliente'] ?? 'N/A'
        ]);
    }
}
