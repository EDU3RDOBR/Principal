<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'id_cliente', 'codigo_cliente', 'nome_razaosocial', 'nome_fantasia',
        'tipo_pessoa', 'cpf_cnpj', 'telefone_primario', 'telefone_secundario',
        'telefone_terciario', 'email_principal', 'email_secundario', 'rg',
        'rg_emissao', 'inscricao_municipal', 'inscricao_estadual',
        'data_cadastro', 'data_nascimento', 'alerta'
    ];
}
