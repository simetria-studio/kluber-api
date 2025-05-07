<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPressPrensa extends Model
{
    use HasFactory;

    protected $table = 'my_press_prensas';

    protected $fillable = [
        'codigo_empresa',
        'tipo_prensa',
        'fabricante',
        'comprimento',
        'espressura',
        'produto',
        'velocidade',
        'produto_cinta',
        'produto_corrente',
        'produto_bendroads',
        'visita_id',
        'torque',
    ];

    public function visita()
    {
        return $this->belongsTo(MyPressVisita::class, 'visita_id', 'id');
    }

    public function elementos()
    {
        return $this->hasMany(MyPressElemento::class, 'mypress_prensa_id', 'id');
    }
}
