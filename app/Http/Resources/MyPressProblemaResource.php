<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPressProblemaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'codigo_empresa' => $this->codigo_empresa,
            'descricao' => $this->descricao,
            'problema_redutor_principal' => $this->problema_redutor_principal,
            'comentario_redutor_principal' => $this->comentario_redutor_principal,
            'problema_temperatura' => $this->problema_temperatura,
            'comentario_temperatura' => $this->comentario_temperatura,
            'problema_tambor_principal' => $this->problema_tambor_principal,
            'comentario_tambor_principal' => $this->comentario_tambor_principal,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
} 