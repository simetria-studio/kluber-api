<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPressAnexo extends Model
{
    use HasFactory;

    protected $table = 'my_press_anexos';

    protected $fillable = [
        'id',
        'codigo_empresa',
        'nome',
        'tipo',
        'url',
        'base64',
        'mypress_comentario_id',
     
        
    ];

    public function comentario()
    {
        return $this->belongsTo(MyPressComentario::class);
    }
}
