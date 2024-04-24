<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('teste66', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_cliente')->nullable();
            $table->string('nome_cliente')->nullable();
            $table->string('data_acesso')->nullable();
            $table->string('origem')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teste66');
    }
};
