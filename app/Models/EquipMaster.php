<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipMaster extends Model
{
    use HasFactory;

    protected $table = 'equipamento_master';

    protected $fillable = [
        'id',
        'codigo_empresa',
        'planta',
        'area',
        'subarea',
        'linha',
        'tag',
        'maquina',
        'conjunto',
        'equipamento',
        'numero_plano',
        'qtde_pontos_total',
        'ativo',
    ];

    public $timestamps = false;
}
