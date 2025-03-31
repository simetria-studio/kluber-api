<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPressVisita extends Model
{
    use HasFactory;

    protected $table = 'my_press_visitas';

    protected $fillable = [
        'codigo_empresa',
        'data_visita',
        'cliente',
        'contato_cliente',
        'contato_kluber'
    ];

    public function prensas()
    {
        return $this->hasMany(MyPressPrensa::class, 'visita_id', 'id');
    }

    public function problemas()
    {
        return $this->hasMany(MyPressProblema::class, 'mypress_visita_id', 'id');
    }
}
