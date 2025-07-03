<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    use HasFactory;

    protected $table = "mensagem";

    protected $fillable = [
        'codigo_empresa',
        'codigo_mensagem',
        'descricao_mensagem',
        'assunto_mensagem',
        'ativo',
    ];

    public $timestamps = false;
}
