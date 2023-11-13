<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PontoLubController extends Controller
{
    public function components()
    {

        $components = [
            [
                'id' => 1,
                'name' => 'ELOS',
                'description' => 'Esse é o componente 1'
            ],


        ];

        return response()->json($components);
    }

    public function condOp()
    {

        $condOp = [
            [
                'id' => 1,
                'name' => 'OPERANDO',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'NÂO OPERANDO',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($condOp);
    }

    public function atividadeBreve()
    {

        $atividadeBreve = [
            [
                'id' => 1,
                'name' => 'REALIZAR LIMPEZA E LUBRIFICAR',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'OUTROS',
                'description' => 'Esse é o componente 1'
            ],

        ];

        return response()->json($atividadeBreve);
    }

    public function unidadeMed()
    {

        $unidadeMed = [
            [
                'id' => 1,
                'name' => 'UN',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'LT',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($unidadeMed);
    }

    public function frequencia()
    {

        $frequencia = [
            [
                'id' => 1,
                'name' => 'DIÁRIO',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'SEMANAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 3,
                'name' => 'QUINZENAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 4,
                'name' => 'MENSAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 5,
                'name' => 'BIMESTRAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 6,
                'name' => 'TRIMESTRAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 7,
                'name' => 'SEMESTRAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 8,
                'name' => 'ANUAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 9,
                'name' => 'BIENAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 10,
                'name' => 'TRIENAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 11,
                'name' => 'QUADRIENAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 12,
                'name' => 'QUINQUENAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 13,
                'name' => 'DECENAL',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 14,
                'name' => 'OUTROS',
                'description' => 'Esse é o componente 1'
            ],
        ];
        return response()->json($frequencia);
    }

    public function material()
    {
            
            $material = [
                [
                    'id' => 1,
                    'name' => 'KLUBEROIL 4 UH1-1500 N SPRAY SAM',
                    'description' => 'Esse é o componente 1'
                ],
                [
                    'id' => 3,
                    'name' => 'OUTROS',
                    'description' => 'Esse é o componente 1'
                ],
            ];
    
            return response()->json($material);
    }

    public function nsf()
    {
        $nsf = [
            [
                'id' => 1,
                'name' => 'H1',
                'description' => 'Esse é o componente 1'
            ],
            [
                'id' => 2,
                'name' => 'OUTROS',
                'description' => 'Esse é o componente 1'
            ],
        ];

        return response()->json($nsf);
    }
}
