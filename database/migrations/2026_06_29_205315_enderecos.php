<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
   public function up(): void {
    Schema::create('enderecos', function (Blueprint $table) {
        $table->id();
        $table->string('logradouro');
        $table->string('numero', 10);
        $table->string('bairro');
        $table->string('cidade');
        $table->char('estado', 2);
        $table->index('cep'); //index serve para ao consultar encontrar o cep mais rapido
        $table->string('complemento')->nullable();
        $table->decimal('latitude',10,7)->nullable();
        $table->decimal('longitude',10,7)->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

   
   public function down(): void
{
    Schema::dropIfExists('enderecos');
}
};
