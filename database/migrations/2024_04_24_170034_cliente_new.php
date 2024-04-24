<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cliente');
            $table->integer('codigo_cliente');
            $table->string('nome_razaosocial');
            $table->string('nome_fantasia')->nullable();
            $table->string('tipo_pessoa');
            $table->string('cpf_cnpj');
            $table->string('telefone_primario')->nullable();
            $table->string('telefone_secundario')->nullable();
            $table->string('telefone_terciario')->nullable();
            $table->string('email_principal')->nullable();
            $table->string('email_secundario')->nullable();
            $table->string('rg')->nullable();
            $table->string('rg_emissao')->nullable();
            $table->string('inscricao_municipal')->nullable();
            $table->string('inscricao_estadual')->nullable();
            $table->dateTime('data_cadastro');
            $table->dateTime('data_nascimento')->nullable();
            $table->boolean('alerta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
