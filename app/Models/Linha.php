<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linha extends Model
{
    use HasFactory;

    protected $table = 'linha';

    protected $fillable = [
        'id',
        'id_subarea',
        'nome_linha',
        'ativo',

    ];
    public $timestamps = false;
}
