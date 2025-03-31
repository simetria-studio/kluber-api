<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MyPressVisita;
use App\Models\MyPressPrensa;
use App\Models\MyPressElemento;
use App\Models\MyPressComentario;
use App\Models\MyPressAnexo;
use App\Models\MyPressTemperatura;
use App\Models\MyPressProblema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MyPressProcessStatus;

class ProcessMyPressData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $jobId;

    public function __construct($data, $jobId)
    {
        $this->data = $data;
        $this->jobId = $jobId;
    }

    public function handle()
    {
        try {
            // Atualiza status para processing
            MyPressProcessStatus::where('job_id', $this->jobId)
                ->update(['status' => 'processing']);

            DB::beginTransaction();

            // 1. Criar a visita
            $visita = MyPressVisita::create([
                'data_visita' => $this->data['visita']['data_visita'] ?? null,
                'cliente' => $this->data['visita']['cliente'] ?? null,
                'contato_cliente' => $this->data['visita']['contato_cliente'] ?? null,
                'contato_kluber' => $this->data['visita']['contato_kluber'] ?? null,
            ]);

            // 2. Criar as prensas
            if (!empty($this->data['prensas'])) {
                foreach ($this->data['prensas'] as $prensaData) {
                    $prensa = MyPressPrensa::create([
                        'tipo_prensa' => $prensaData['prensa']['tipo_prensa'] ?? null,
                        'fabricante' => $prensaData['prensa']['fabricante'] ?? null,
                        'comprimento' => $prensaData['prensa']['comprimento'] ?? null,
                        'espressura' => $prensaData['prensa']['espessura'] ?? null,
                        'produto' => $prensaData['prensa']['produto'] ?? null,
                        'velocidade' => $prensaData['prensa']['velocidade'] ?? null,
                        'produto_cinta' => $prensaData['prensa']['produto_cinta'] ?? null,
                        'produto_corrente' => $prensaData['prensa']['produto_corrente'] ?? null,
                        'produto_bendroads' => $prensaData['prensa']['produto_bendroads'] ?? null,
                        'visita_id' => $visita->id,
                    ]);

                    // Processa elementos, comentários, anexos e temperaturas...
                    $this->processElementos($prensaData, $prensa);
                }
            }

            // 7. Criar problemas
            if (!empty($this->data['problemas'])) {
                foreach ($this->data['problemas'] as $problemaData) {
                    MyPressProblema::create([
                        'problema_redutor_principal' => $problemaData['problema_redutor_principal'] ?? null,
                        'comentario_redutor_principal' => $problemaData['comentario_redutor_principal'] ?? null,
                        'problema_temperatura' => $problemaData['problema_temperatura'] ?? null,
                        'comentario_temperatura' => $problemaData['comentario_temperatura'] ?? null,
                        'problema_tambor_principal' => $problemaData['problema_tambor_principal'] ?? null,
                        'comentario_tambor_principal' => $problemaData['comentario_tambor_principal'] ?? null,
                        'mypress_visita_id' => $visita->id,
                    ]);
                }
            }

            DB::commit();

            // Atualiza status para completed
            MyPressProcessStatus::where('job_id', $this->jobId)
                ->update([
                    'status' => 'completed',
                    'visita_id' => $visita->id
                ]);

            Log::info('Dados processados com sucesso', [
                'visita_id' => $visita->id,
                'job_id' => $this->jobId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Atualiza status para failed
            MyPressProcessStatus::where('job_id', $this->jobId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);

            Log::error('Erro ao processar dados:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'job_id' => $this->jobId
            ]);
            
            throw $e;
        }
    }

    private function processElementos($prensaData, $prensa)
    {
        if (!empty($prensaData['elementos'])) {
            foreach ($prensaData['elementos'] as $elementoData) {
                $elemento = MyPressElemento::create([
                    'consumo_nominal' => $elementoData['elemento']['consumo1'] ?? null,
                    'consumo_real' => $elementoData['elemento']['consumo2'] ?? null,
                    'consumo_real_adicional' => $elementoData['elemento']['consumo3'] ?? null,
                    'toma_consumo_real' => $elementoData['elemento']['toma'] ?? null,
                    'posicao' => $elementoData['elemento']['posicao'] ?? null,
                    'tipo' => $elementoData['elemento']['tipo'] ?? null,
                    'mypress' => $elementoData['elemento']['mypress'] ?? null,
                    'mypress_prensa_id' => $prensa->id,
                ]);

                $this->processComentarios($elementoData, $elemento);
                $this->processTemperaturas($elementoData, $elemento);
            }
        }
    }

    private function processComentarios($elementoData, $elemento)
    {
        if (!empty($elementoData['comentarios'])) {
            foreach ($elementoData['comentarios'] as $comentarioData) {
                $comentario = MyPressComentario::create([
                    'comentario' => $comentarioData['comentario']['comentario'] ?? null,
                    'mypress_elemento_id' => $elemento->id,
                ]);

                // Processa anexos
                if (!empty($comentarioData['anexos'])) {
                    foreach ($comentarioData['anexos'] as $anexoData) {
                        MyPressAnexo::create([
                            'nome' => $anexoData['nome'] ?? null,
                            'tipo' => $anexoData['tipo'] ?? null,
                            'url' => $anexoData['url'] ?? null,
                            'base64' => $anexoData['base64'] ?? null,
                            'mypress_comentario_id' => $comentario->id,
                        ]);
                    }
                }
            }
        }
    }

    private function processTemperaturas($elementoData, $elemento)
    {
        if (!empty($elementoData['temperaturas'])) {
            foreach ($elementoData['temperaturas'] as $temperaturaData) {
                MyPressTemperatura::create([
                    'data_registro' => $temperaturaData['data_registro'] ?? now(),
                    'zona1' => $temperaturaData['zona1'] ?? null,
                    'zona2' => $temperaturaData['zona2'] ?? null,
                    'zona3' => $temperaturaData['zona3'] ?? null,
                    'zona4' => $temperaturaData['zona4'] ?? null,
                    'zona5' => $temperaturaData['zona5'] ?? null,
                    'mypress_elemento_id' => $elemento->id
                ]);
            }
        }
    }

    // Métodos auxiliares para processar cada tipo de dado...
} 