<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPressComentarioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'comentario' => $this->comentario,
            'anexos' => MyPressAnexoResource::collection($this->whenLoaded('anexos'))
        ];
    }
} 