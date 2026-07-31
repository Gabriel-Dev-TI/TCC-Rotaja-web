<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void {
    Schema::create('entregas', function (Blueprint $table) {
        $table->id();
        $table->string('nome_produto');
        $table->enum('status', ['pendente', 'aceita', 'em_transito', 'concluido', 'cancelado'])->default('pendente');
        $table->decimal('preco', 10, 2);
        $table->decimal('largura', 10, 2);
        $table->decimal('altura', 10, 2);
        $table->decimal('peso', 10, 2);
        $table->decimal('distancia',8,2)->nullable();
        $table->integer('tempo_estimado_minutos')->nullable();
        $table->text('descricao')->nullable();
        $table->text('observacoes')->nullable();
        $table->timestamps();
        $table->softDeletes();

        $table->foreignId('empresa_id')->constrained('empresas');
        $table->foreignId('entregador_id')->nullable()->constrained('entregadores');
        $table->foreignId('endereco_origem_id')->constrained('enderecos');
        $table->foreignId('endereco_destino_id')->constrained('enderecos');
        
    });
}

    
    public function down(): void
{
    Schema::dropIfExists('entregas');
}
};
