<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyPressStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'visita.data_visita' => 'required|date',
            'visita.cliente' => 'required|string',
            'visita.contato_cliente' => 'required|string',
            'visita.contato_kluber' => 'required|string',
            'prensas.*.prensa.tipo_prensa' => 'required|string',
            'prensas.*.prensa.fabricante' => 'required|string',
            'prensas.*.prensa.comprimento' => 'required|numeric',
            'prensas.*.prensa.espessura' => 'required|numeric',
            'prensas.*.prensa.tipo_prensa' => 'required|string',
            'prensas.*.prensa.fabricante' => 'required|string',
            'prensas.*.prensa.comprimento' => 'required|numeric',
            'prensas.*.prensa.espessura' => 'required|numeric',
            // Adicione mais regras conforme necessário
        ];
    }
}