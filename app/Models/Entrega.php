<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entrega extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'entregas';

    protected $fillable = [
    'nome_produto',
    'comprimento',
    'descricao',
    'status',
    'preco',
    'largura',
    'altura',
    'peso',
    'distancia',
    'tempo_estimado_minutos',
    'observacoes',

    'empresa_id',
    'entregador_id',
    'endereco_origem_id',
    'endereco_destino_id',
    ];

    protected function casts(): array
    {
        return [
          'preco' => 'decimal:2',
           'peso' => 'decimal:2',
           'altura' => 'decimal:2',
           'largura' => 'decimal:2',
           'distancia' => 'decimal:2',
        ];
    }

    public function getPrecoFormatadoAttribute()
    {
     return number_format($this->preco, 2, ',', '.');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function entregador()
    {
        return $this->belongsTo(Entregador::class, 'entregador_id');
    }

    public function enderecoOrigem()
    {
        return $this->belongsTo(Endereco::class, 'endereco_origem_id');
    }

    public function enderecoDestino()
    {
        return $this->belongsTo(Endereco::class, 'endereco_destino_id');
    }
}