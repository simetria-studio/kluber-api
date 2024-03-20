<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtvComponente extends Model
{
    use HasFactory;

    protected $table = 'atividade_componente';

    protected $fillable = [
        'id',
        'componente',
        'sequencia',
        'qtde_pontos',
        'condicao_operacional',
        'descritivo_simplificado',
        'frequencia',
        'periodicidade',
        'tempo_atividade',
        'qtde_pessoas',
        'qtde_material',
        'codigo_produto',
        'data_hora_alteracao',
        'ativo',
        
    ];

    public $timestamps = false;
}
