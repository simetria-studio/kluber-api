<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'codigo_empresa' => $this->codigo_empresa,
            'codigo_cliente' => $this->codigo_cliente,
            'razao_social' => $this->razao_social,
            'nome_fantasia' => $this->nome_fantasia,
            'email' => $this->email,
            'ativo' => $this->ativo,
        ];
    }
} 