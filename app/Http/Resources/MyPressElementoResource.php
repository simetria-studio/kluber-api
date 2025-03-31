<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPressElementoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'codigo_empresa' => $this->codigo_empresa,
            'consumo_nominal' => $this->consumo_nominal,
            'consumo_real' => $this->consumo_real,
            'consumo_real_adicional' => $this->consumo_real_adicional,
            'toma_consumo_real' => $this->toma_consumo_real,
            'posicao' => $this->posicao,
            'tipo' => $this->tipo,
            'mypress' => $this->mypress,
            'zona1' => $this->zona1,
            'zona2' => $this->zona2,
            'zona3' => $this->zona3,
            'zona4' => $this->zona4,
            'zona5' => $this->zona5,
            'temperaturas' => MyPressTemperaturaResource::collection($this->whenLoaded('temperaturas')),
            'comentarios' => MyPressComentarioResource::collection($this->whenLoaded('comentarios'))
        ];
    }
} 