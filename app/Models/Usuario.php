<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "usuario";

    protected $fillable = [
        "nome_usuario",
        "senha",
        "password",
        "email",
        "sexo",
        "endereco",
        "telefone",
        "cep",
        "cidade",
        "uf",
        "pais",
        "codigo_cliente",
        "bloqueio",
        "codigo_repres",
        "codigo_vend",
        "codigo_funcionario",
        "codigo_ccusto",
        "codigo_fornecedor",
        "codigo_recurso_usu",
        "codigo_empresa",
        "acesso_incorreto",
        "nivel_usuario",
        "data_acesso",
        "usuario_sessao",
        "usuario_ip",
        "data_ultima_troca",
        "pode_alterar_senha",
        "pode_alterar_financeiro",
        "pode_excluir_item_pedido",
        "caixa_atual",
        "impressora_padrao",
        "senha_email",
        "senha_opaf",
        "lingua_usuario",
        "nome_usuario_completo",
        "nivel_usuario_opaf",
        "codigo_familia_usuario",
        "data_acesso_inicial",
        "data_acesso_final",
        "hora_acesso_inicial",
        "hora_acesso_final",
        "ip_inicial",
        "ip_final",
        "bairro",
        "cpf",
        "rg",
        "local_atendimento",
        "codigo_especialidade",
        "codigo_medico",
        "marca_emergencia",
        "ramal",
        "usuario_nivel_padrao",
        "developer_api_key",
        "hora_acesso_sabado_inicial",
        "hora_acesso_sabado_final",
        "hora_acesso_domingo_inicial",
        "hora_acesso_domingo_final",
        "numero_organizacao",
        "created_at",
        "updated_at",
        "access_token",
        "token_expires_in",
        "data_solicitacao_acesso",
    ];

    protected $hidden = [
        'senha',
        'password',
    ];
}
