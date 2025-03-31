<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPressPrensaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tipo_prensa' => $this->tipo_prensa,
            'fabricante' => $this->fabricante,
            'comprimento' => $this->comprimento,
            'espessura' => $this->espessura,
            'produto' => $this->produto,
            'velocidade' => $this->velocidade,
            'produto_cinta' => $this->produto_cinta,
            'produto_corrente' => $this->produto_corrente,
            'produto_bendroads' => $this->produto_bendroads,
            'elementos' => MyPressElementoResource::collection($this->whenLoaded('elementos'))
        ];
    }
} 