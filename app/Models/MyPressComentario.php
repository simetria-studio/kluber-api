<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPressComentario extends Model
{
    use HasFactory;

    protected $table = 'my_press_comentarios';

    protected $fillable = [
        'codigo_empresa',
        'comentario',
        'mypress_elemento_id'
    ];

    public function elemento()
    {
        return $this->belongsTo(MyPressElemento::class, 'mypress_elemento_id', 'id');
    }

    public function anexos()
    {
        return $this->hasMany(MyPressAnexo::class, 'mypress_comentario_id', 'id');
    }
}
