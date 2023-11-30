<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;
    protected $table = 'plano_lubrificacao';
    protected $fillable = [
        'codigo_empresa',
        'numero_plano',
        'codigo_unidade',
        'versao_plano',
        'data_plano',
        'data_revisao',
        'nome_supervisor',
        'nome_lubrificador',
        'responsavel_kluber',
        'data_hora_alteracao',
        'ativo',
        'codigo_mobile'
    ];

    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo_unidade', 'codigo_cliente');
    }
}
