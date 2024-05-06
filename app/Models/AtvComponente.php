<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtvComponente extends Model
{
    use HasFactory;

    protected $table = 'ponto_lubrificacao';

    protected $fillable = [
        'id',
        'codigo_empresa',
        'id_equipamento',
        'numero_ponto',
        'componente',
        'numero_plano',
        'qtde_pontos',
        'condicao_operacional',
        'descritivo_simplificado',
        'descritivo_longo',
        'frequencia',
        'tempo_atividade',
        'qtde_pessoas',
        'qtde_material',
        'unidade_medida',
        'material',
        'codigo_produto',
        'nsf',
        'data_ultimo_lancamento',
        'ativo',

    ];

    public $timestamps = false;
}
