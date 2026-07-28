<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required',
        ]);

        $email = trim($request->email);
        $senha = trim($request->senha);

        $usuario = User::where('email', $email)->first();

        if (!$usuario || !Hash::check($senha, $usuario->senha)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'E-mail ou senha incorretos.'
            ], 401);
        }

        $usuario->tokens()->delete();
        $token = $usuario->createToken('flutter')->plainTextToken;

        return response()->json([
            'sucesso' => true,
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'cargo' => $usuario->cargo,
            ]
        ], 200);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Sessão encerrada com sucesso.'
        ],200);
    }

    public function meuPerfil()
    {
    $usuario = Auth::user();

    $documento = null;
    if ($usuario->entregador) {
        $documento = $usuario->entregador->cpf;
    } elseif ($usuario->empresa) {
        $documento = $usuario->empresa->cnpj;
    }

    return response()->json([
        'sucesso' => true,
        'dados' => [
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'telefone' => $usuario->telefone,
            'cargo' => $usuario->cargo,
            'documento' => $documento, 
            'criado_em' => $usuario->created_at->format('d/m/Y'),
        ]
    ]);
    }

    public function atualizarSenha(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => 'required|min:6|confirmed',
        ]);

        $usuario = Auth::user();

        if (!Hash::check($request->senha_atual, $usuario->senha)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'A senha atual está incorreta.'
            ], 422);
        }

        $usuario->update([
            'senha' => Hash::make($request->nova_senha)
        ]);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Senha alterada com sucesso!'
        ]);
    }

    public function historico()
{
    $usuario = Auth::user();

    $historico = Entrega::select([
        'id', 
        'status',
        'preco',
        'created_at',
        'endereco_origem_id',
        'endereco_destino_id',
        'empresa_id',
        'entregador_id',
    ])->with([
        'enderecoOrigem:id,logradouro,numero',
        'enderecoDestino:id,logradouro,numero'
    ])->where(function ($query) use ($usuario) {
        if ($usuario->cargo === 'empresa' && $usuario->empresa) {
            $query->where('empresa_id', $usuario->empresa->id);
        } elseif ($usuario->cargo === 'entregador' && $usuario->entregador) {
            $query->where('entregador_id', $usuario->entregador->id);
        }
    })
    ->latest()
    ->get()->map(function ($entrega) {
        // Cria os campos formatados para o JSON
        $entrega->data = $entrega->created_at ? $entrega->created_at->format('d/m/Y') : null;
        $entrega->hora = $entrega->created_at ? $entrega->created_at->format('H:i') : null;

        return $entrega;
    });

    $historico->makeHidden([
        'endereco_origem_id', 
        'endereco_destino_id',
        'empresa_id',
        'entregador_id'
    ]);

    return response()->json([
        'sucesso' => true,
        'dados' => $historico
    ]);
}
}