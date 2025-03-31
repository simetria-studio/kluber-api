<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MyPressVisita;
use App\Models\MyPressPrensa;
use App\Models\MyPressElemento;
use App\Models\MyPressComentario;
use App\Models\MyPressAnexo;
use App\Models\MyPressTemperatura;
use App\Models\MyPressProblema;
use App\Http\Requests\MyPressStoreRequest;
use App\Http\Resources\MyPressVisitaResource;

class MyPressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $visitas = MyPressVisita::with([
                'prensas.elementos.temperaturas',
                'prensas.elementos.comentarios.anexos',
                'problemas'
            ])
            ->orderBy('data_visita', 'desc')
            ->paginate(10); // Adicionando paginação

            if ($visitas->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma visita encontrada'
                ], 404);
            }

            return MyPressVisitaResource::collection($visitas)
                ->additional([
                    'message' => 'Visitas encontradas com sucesso'
                ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao listar visitas:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'message' => 'Erro ao listar visitas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MyPressStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            \Log::info('Dados recebidos:', ['request' => $request->all()]);

            // 1. Criar a visita
            $visita = MyPressVisita::create([
                'codigo_empresa' => '0001',
                'data_visita' => $request->visita['data_visita'] ?? null,
                'cliente' => $request->visita['cliente'] ?? null,
                'contato_cliente' => $request->visita['contato_cliente'] ?? null,
                'contato_kluber' => $request->visita['contato_kluber'] ?? null,
            ]);

            // 2. Criar as prensas
            if (!empty($request->prensas)) {
                foreach ($request->prensas as $prensaData) {
                    $prensa = MyPressPrensa::create([
                        'codigo_empresa' => '0001',
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

                    // 3. Criar os elementos para cada prensa
                    if (!empty($prensaData['elementos'])) {
                        foreach ($prensaData['elementos'] as $elementoData) {
                            $elemento = MyPressElemento::create([
                                'codigo_empresa' => '0001',
                                'consumo_nominal' => $elementoData['elemento']['consumo1'] ?? null,
                                'consumo_real' => $elementoData['elemento']['consumo2'] ?? null,
                                'consumo_real_adicional' => $elementoData['elemento']['consumo3'] ?? null,
                                'toma_consumo_real' => $elementoData['elemento']['toma'] ?? null,
                                'posicao' => $elementoData['elemento']['posicao'] ?? null,
                                'tipo' => $elementoData['elemento']['tipo'] ?? null,
                                'mypress' => $elementoData['elemento']['mypress'] ?? null,
                                'mypress_prensa_id' => $prensa->id,
                            ]);

                            // 4. Criar comentários para cada elemento
                            if (!empty($elementoData['comentarios'])) {
                                foreach ($elementoData['comentarios'] as $comentarioData) {
                                    $comentario = MyPressComentario::create([
                                        'codigo_empresa' => '0001',
                                        'comentario' => $comentarioData['comentario']['comentario'] ?? null,
                                        'mypress_elemento_id' => $elemento->id,
                                    ]);

                                    // 5. Criar anexos para cada comentário
                                    if (!empty($comentarioData['anexos'])) {
                                        foreach ($comentarioData['anexos'] as $anexoData) {
                                            MyPressAnexo::create([
                                                'codigo_empresa' => '0001',
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

                            // 6. Criar temperaturas para cada elemento
                            if (!empty($elementoData['temperaturas'])) {
                                foreach ($elementoData['temperaturas'] as $temperaturaData) {
                                    MyPressTemperatura::create([
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
                    }
                }
            }

            // 7. Criar problemas
            if (!empty($request->problemas)) {
                foreach ($request->problemas as $problemaData) {
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

            return response()->json([
                'message' => 'Visita cadastrada com sucesso',
                'visita_id' => $visita->id
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao cadastrar visita:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Erro ao cadastrar visita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
