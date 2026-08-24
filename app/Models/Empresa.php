<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes;

    protected $table = 'empresas';

    protected $fillable = [
        'cnpj',
        'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'empresa_id');
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'empresa_id');
    }
}