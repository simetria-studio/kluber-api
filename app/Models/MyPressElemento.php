<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPressElemento extends Model
{
    use HasFactory;

    protected $table = 'my_press_elementos';

    protected $fillable = [
        'codigo_empresa',
        'consumo_nominal',
        'consumo_real',
        'consumo_real_adicional',
        'toma_consumo_real',
        'posicao',
        'tipo',
        'mypress',
        'mypress_prensa_id',
        'zona1',
        'zona2',
        'zona3',
        'zona4',
        'zona5'
    ];

    public function prensa()
    {
        return $this->belongsTo(MyPressPrensa::class, 'mypress_prensa_id', 'id');
    }

    public function comentarios()
    {
        return $this->hasMany(MyPressComentario::class, 'mypress_elemento_id', 'id');
    }

    public function temperaturas()
    {
        return $this->hasMany(MyPressTemperatura::class, 'mypress_elemento_id', 'id');
    }
}
