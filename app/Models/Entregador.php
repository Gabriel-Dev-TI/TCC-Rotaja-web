<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entregador extends Model
{
    use SoftDeletes;

    protected $table = 'entregadores'; 

    protected $fillable = [
        'cpf',
        'tipo_veiculo',
        'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'entregador_id');
    }
}