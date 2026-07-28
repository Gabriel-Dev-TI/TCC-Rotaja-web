<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void {
    Schema::create('empresas', function (Blueprint $table) {
        $table->id();
        $table->string('cnpj', 18)->unique();
        $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
        $table->foreignId('endereco_id')->constrained('enderecos')->onDelete('cascade');
        $table->timestamps();
        $table->softDeletes();
    });
}

    
    public function down(): void
{
    Schema::dropIfExists('empresas');
}
};
