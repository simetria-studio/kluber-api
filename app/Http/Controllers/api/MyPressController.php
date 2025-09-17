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
    public function create(Request $request)
    {
        \Log::info('Dados recebidos:', ['request' => $request->all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        try {
            DB::beginTransaction();

            \Log::info('Dados recebidos:', ['request' => $request->all()]);

            // Get the actual request data from the nested structure
            $requestData = $request->all();
            $requestData = $requestData['request'] ?? $requestData;

            // 1. Criar a visita
            $visita = MyPressVisita::create([
                'codigo_empresa' => '0001',
                'data_visita' => isset($requestData['visita']['data_visita']) ? \Carbon\Carbon::parse($requestData['visita']['data_visita'])->format('Y-m-d') : null,
                'cliente' => $requestData['visita']['cliente'] ?? null,
                'contato_cliente' => $requestData['visita']['contato_cliente'] ?? null,
                'contato_kluber' => $requestData['visita']['contato_kluber'] ?? null,
            ]);

            // 2. Criar as prensas
            if (!empty($requestData['prensas'])) {
                foreach ($requestData['prensas'] as $prensaData) {
                    $prensa = MyPressPrensa::create([
                        'codigo_empresa' => '0001',
                        'tipo_prensa' => $prensaData['prensa']['tipo_prensa'] ?? null,
                        'fabricante' => $prensaData['prensa']['fabricante'] ?? null,
                        'comprimento' => $prensaData['prensa']['comprimento'] ?? null,
                        'espressura' => $prensaData['prensa']['espressura'] ?? null,
                        'produto' => $prensaData['prensa']['produto'] ?? null,
                        'velocidade' => $prensaData['prensa']['velocidade'] ?? null,
                        'produto_cinta' => $prensaData['prensa']['produto_cinta'] ?? null,
                        'produto_corrente' => $prensaData['prensa']['produto_corrente'] ?? null,
                        'produto_bendroads' => $prensaData['prensa']['produto_bendroads'] ?? null,
                        'torque' => $prensaData['prensa']['torque'] ?? null,
                        'largura' => $prensaData['prensa']['largura'] ?? null,
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
                                'mypress_prensa_id' => $prensa->id,
                            ]);

                            // 4. Criar comentários para cada elemento
                            if (!empty($elementoData['comentarios'])) {
                                $comentarios = $elementoData['comentarios'];

                                // Ensure comentarios is an array
                                if (!is_array($comentarios)) {
                                    $comentarios = [$comentarios];
                                }

                                foreach ($comentarios as $comentarioData) {
                                    // Skip if comentarioData is not an array
                                    if (!is_array($comentarioData)) {
                                        continue;
                                    }

                                    // Handle nested comentario structure
                                    $comentarioText = '';
                                    if (isset($comentarioData['comentario'])) {
                                        if (is_array($comentarioData['comentario'])) {
                                            $comentarioText = $comentarioData['comentario']['comentario'] ?? '';
                                        } else {
                                            $comentarioText = $comentarioData['comentario'];
                                        }
                                    }

                                    $comentario = MyPressComentario::create([
                                        'codigo_empresa' => '0001',
                                        'comentario' => $comentarioText,
                                        'mypress_elemento_id' => $elemento->id,
                                    ]);

                                    // 5. Criar anexos para cada comentário
                                    if (!empty($comentarioData['anexos'])) {
                                        $anexos = $comentarioData['anexos'];

                                        // Handle different types of anexos data
                                        if (is_string($anexos)) {
                                            // If anexos is a string, create a single anexo with that string as the name
                                            MyPressAnexo::create([
                                                'codigo_empresa' => '0001',
                                                'nome' => $anexos,
                                                'tipo' => null,
                                                'url' => null,
                                                'base64' => null,
                                                'mypress_comentario_id' => $comentario->id,
                                            ]);
                                        } elseif (is_array($anexos)) {
                                            // If anexos is an array, process each item
                                            foreach ($anexos as $anexoData) {
                                                // Handle both string and array anexoData
                                                if (is_string($anexoData)) {
                                                    // If anexoData is a string, use it as the name
                                                    MyPressAnexo::create([
                                                        'codigo_empresa' => '0001',
                                                        'nome' => $anexoData,
                                                        'tipo' => null,
                                                        'url' => null,
                                                        'base64' => null,
                                                        'mypress_comentario_id' => $comentario->id,
                                                    ]);
                                                } elseif (is_array($anexoData)) {
                                                    // If anexoData is an array, use its fields
                                                    MyPressAnexo::create([
                                                        'codigo_empresa' => '0001',
                                                        'nome' => $anexoData['nome'] ?? '',
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
                            }
                        }
                    }
                    \Log::info('Temperaturas:', ['temperaturas' => $prensaData['temperaturas']]);
                    // 6. Criar temperaturas para cada prensa
                    if (!empty($prensaData['temperaturas'])) {
                        foreach ($prensaData['temperaturas'] as $temperaturaData) {
                            MyPressTemperatura::create([
                                'zona1' => $temperaturaData['zona1'] ?? null,
                                'zona2' => $temperaturaData['zona2'] ?? null,
                                'zona3' => $temperaturaData['zona3'] ?? null,
                                'zona4' => $temperaturaData['zona4'] ?? null,
                                'zona5' => $temperaturaData['zona5'] ?? null,
                                'mypress_elemento_id' => $prensa->id
                            ]);
                        }
                    }
                }
            }

            // 7. Criar problemas
            if (!empty($requestData['problemas'])) {
                foreach ($requestData['problemas'] as $problemaData) {
                    MyPressProblema::create([
                        'produto_redutor_principal' => $problemaData['lubrificante_redutor_principal'] ?? null,
                        'problema_redutor_principal' => $problemaData['problema_redutor_principal'] ?? null,
                        'comentario_redutor_principal' => $problemaData['comentario_redutor_principal'] ?? null,
                        'produto_temperatura' => $problemaData['graxa_rolamentos_zonas_quentes'] ?? null,
                        'problema_temperatura' => $problemaData['problema_temperatura'] ?? null,
                        'comentario_temperatura' => $problemaData['comentario_temperatura'] ?? null,
                        'produto_tambor_principa' => $problemaData['graxa_tambor_principal'] ?? null,
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
