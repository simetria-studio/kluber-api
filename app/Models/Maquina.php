<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
    use HasFactory;

    protected $table = 'maquina';

    protected $fillable = [
        'id',
        'id_linha',
        'tag',
        'nome_maquina',
        'ativo',
    ];

    public $timestamps = false;
}
