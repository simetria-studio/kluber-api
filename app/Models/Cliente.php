<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = "cliente";

    protected $fillable = [
        'id',
        'codigo_empresa',
        'codigo_cliente',
        'codigo_cliente_laboratorio',
        'razao_social',
        'nome_fantasia',
        'tipo_pessoa',
        'cnpj_cpf',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cep',
        'cidade',
        'pais',
        'uf',
        'ddd_telefone',
        'telefone',
        'e_mail',
        'tipo_cliente',
        'cliente_master',
        'codigo_regiao',
        'setor_mercado',
        'assessor_tecnico',
        'data_cadastro',
        'data_alteracao',
        'usuario_cadastro',
        'mensagem_padrao_cliente',
        'email',
        'ativo',

    ];
}
