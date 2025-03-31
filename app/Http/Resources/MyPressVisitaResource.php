<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPressVisitaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'codigo_empresa' => $this->codigo_empresa,
            'data_visita' => $this->data_visita,
            'cliente' => $this->cliente,
            'contato_cliente' => $this->contato_cliente,
            'contato_kluber' => $this->contato_kluber,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'prensas' => MyPressPrensaResource::collection($this->whenLoaded('prensas')),
            'problemas' => MyPressProblemaResource::collection($this->whenLoaded('problemas'))
        ];
    }
} 