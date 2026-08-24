<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Endereco extends Model
{
    use SoftDeletes;

    protected $table = 'enderecos';

    protected $fillable = [
        'empresa_id',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'complemento',
        'latitude',
        'longitude',
        'tipo',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function entregasOrigem()
    {
        return $this->hasMany(Entrega::class, 'endereco_origem_id');
    }

    public function entregasDestino()
    {
        return $this->hasMany(Entrega::class, 'endereco_destino_id');
    }
}