<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPressTemperaturaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'zona1' => $this->zona1,
            'zona2' => $this->zona2,
            'zona3' => $this->zona3,
            'zona4' => $this->zona4,
            'zona5' => $this->zona5,
            'data_registro' => $this->data_registro
        ];
    }
} 