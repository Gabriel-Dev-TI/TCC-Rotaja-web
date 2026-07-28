<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entrega;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EntregaController extends Controller
{
    public function index()
{
    $usuario = Auth::user();

    //Para o entregador retorna as entregas pendentes e as que ele aceitou
    if ($usuario->cargo == 'entregador') {
        if (!$usuario->entregador) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Perfil de entregador não encontrado.'
            ], 404);
        }

        $entregadorId = $usuario->entregador->id;

        $entregas = Entrega::with(['empresa', 'enderecoOrigem', 'enderecoDestino'])
            ->where(function ($query) use ($entregadorId) {
                $query->where('status', 'pendente')
                      ->orWhere(function ($q) use ($entregadorId) {
                          $q->where('entregador_id', $entregadorId)
                            ->where('status', 'em_transito');
                      });
            })
            ->latest()
            ->get();

        return response()->json([
            'sucesso' => true,
            'dados' => $entregas
        ]);
    }

    //Para a empresa retorna entregas pendentes e as em transito
    if ($usuario->cargo == 'empresa') {
        if (!$usuario->empresa) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Perfil de empresa não encontrado.'
            ], 404);
        }

        $entregas = Entrega::with(['entregador', 'enderecoOrigem', 'enderecoDestino'])
            ->where('empresa_id', $usuario->empresa->id)
            ->whereIn('status', ['pendente', 'em_transito'])
            ->latest()
            ->get();

        return response()->json([
            'sucesso' => true,
            'dados' => $entregas
        ]);
    }
    
    return response()->json([
        'sucesso' => false,
        'mensagem' => 'Usuário não tem permissão para consultar entregas'
    ], 403);
}


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'largura' => 'required|numeric',
        'altura' => 'required|numeric',
        'peso' => 'required|numeric',
        'preco' => 'nullable|numeric',
        
        // Validação do objeto de Endereço de Origem
        'origem' => 'required|array',
        'origem.logradouro' => 'required|string|max:255',
        'origem.numero' => 'required|string|max:10',
        'origem.bairro' => 'required|string|max:255',
        'origem.cidade' => 'required|string|max:255',
        'origem.estado' => 'required|string',
        'origem.cep' => 'required|string|max:9',
        'origem.complemento' => 'nullable|string|max:255',

        // Validação do objeto de Endereço de Destino
        'destino' => 'required|array',
        'destino.logradouro' => 'required|string|max:255',
        'destino.numero' => 'required|string|max:10',
        'destino.bairro' => 'required|string|max:255',
        'destino.cidade' => 'required|string|max:255',
        'destino.estado' => 'required|string',
        'destino.cep' => 'required|string|max:9',
        'destino.complemento' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'sucesso' => false,
            'mensagem' => $validator->errors()->first()
        ], 422);
    }

    $dados = $validator->validated();
    $usuario = Auth::user();

    if (!$usuario || !$usuario->empresa) {
        return response()->json([
            'sucesso' => false, 
            'mensagem' => 'Usuário não possui uma empresa associada.'
        ], 403);
    }

    return DB::transaction(function () use ($dados, $usuario) {
        
        $enderecoOrigem = Endereco::create($dados['origem']);
        $enderecoDestino = Endereco::create($dados['destino']);

        $entrega = Entrega::create([
            'empresa_id' => $usuario->empresa->id,
            'endereco_origem_id' => $enderecoOrigem->id,
            'endereco_destino_id' => $enderecoDestino->id,
            'largura' => $dados['largura'],
            'altura' => $dados['altura'],
            'peso' => $dados['peso'],
            'preco' => $dados['preco'] ?? 0.0,
            'status' => 'pendente',
        ]);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Entrega cadastrada com sucesso!',
            'dados' => $entrega
        ], 201);
    });
}
   

    public function show($id)
    {
        $entrega = Entrega::with(['empresa', 'entregador', 'enderecoOrigem', 'enderecoDestino'])->findOrFail($id);

        return response()->json(['sucesso' => true, 'dados' => $entrega]);
    }

    public function update(Request $request, $id)
    {
        $entrega = Entrega::findOrFail($id);
        $entrega->update($request->all());

        return response()->json(['sucesso' => true, 'dados' => $entrega]);
    }

    public function destroy($id)
    {
        $entrega = Entrega::findOrFail($id);
        $entrega->delete();

        return response()->json(['sucesso' => true, 'mensagem' => 'Entrega removida com sucesso']);
    }
}