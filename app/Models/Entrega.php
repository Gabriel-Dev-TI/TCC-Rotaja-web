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
        'status',
        'preco',
        'largura',
        'altura',
        'peso',
        'observacoes',
        'empresa_id',
        'entregador_id',
        'endereco_origem_id',
        'endereco_destino_id',
    ];

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