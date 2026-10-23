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
        // Verifica se existe email com a senha fornecida no banco de dados

        $request->validate([
            'email' => 'required|email',
            'senha' => 'required',
        ]);

        $email = trim($request->email);
        $senha = trim($request->senha);

        $usuario = User::where('email', $email)->first();

        if (!$usuario || !Hash::check($senha, $usuario->senha)) {
            return response()->json([
                'mensagem' => 'E-mail ou senha incorretos.'
            ], 401);
        }

        // Cria token para proximas requisições autenticadas
        $usuario->tokens()->delete();
        $token = $usuario->createToken('flutter')->plainTextToken;

        // Para o entregador retorna o total de ganhos (soma do preco das entregas que foram concluidas) para listar
        // Se for empresa retorna 0, pois no movel so lista os ganhos do entregador
        $totalGanhosEntregador = 0;

        if ($usuario->cargo === 'empresa') {
        $usuario->load('empresa.enderecos');
        }

        if ($usuario->cargo === 'entregador') {
            $usuario->load('entregador');

            $totalGanhosEntregador = Entrega::where('entregador_id', $usuario->entregador->id)
                ->where('status', 'concluido')
                ->whereNull('deleted_at')
                ->sum('preco');
        }

        $dados = $usuario->toArray();
        $dados['total_ganhos'] = $totalGanhosEntregador;

        return response()->json([
            'token' => $token,
            'usuario' => $dados,
        ], 200);
    }

    public function logout()
    {
        // Deleta o token de autenticação
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'mensagem' => 'Sessão encerrada com sucesso.'
        ],200);
    }

    public function verificaDados()
    {
        // Retorna todos os dados da conta e endereço caso tenha alguma atualização feita pelo web
        $usuario = Auth::user();

        // Para o entregador retorna o total de ganhos (soma do preco das entregas que foram concluidas) para listar
        // Se for empresa retorna 0, pois no movel so lista os ganhos do entregador
        $totalGanhosEntregador = 0;

        if ($usuario->cargo === 'empresa') {
            $usuario->load('empresa.enderecos');
        }

        if ($usuario->cargo === 'entregador') {
            $usuario->load('entregador');

            $totalGanhosEntregador = Entrega::where('entregador_id', $usuario->entregador->id)
                ->where('status', 'concluido')
                ->whereNull('deleted_at')
                ->sum('preco');
        }

        $dados = $usuario->toArray();
        $dados['total_ganhos'] = $totalGanhosEntregador;

        return response()->json([
            'dados' => $dados,
        ], 200);
    }   

   public function atualizarPerfil(Request $request)
    {
        // Atualiza nome, email e telefone
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email,' . $request->user()->id],
            'telefone' => ['required', 'string'],
        ]);

        $user = $request->user();
        $user->update($request->only(['nome', 'email', 'telefone']));

        return response()->json([
            'mensagem' => 'Perfil atualizado com sucesso.',
            'dados' => [
                'nome' => $user->nome,
                'email' => $user->email,
                'telefone' => $user->telefone,
            ],
        ], 200);
    }      

    public function atualizarSenha(Request $request)
    {
        // Atualiza senha
        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => 'required|min:8|confirmed',
        ]);

        $usuario = Auth::user();

        if (!Hash::check($request->senha_atual, $usuario->senha)) {
            return response()->json([
                'mensagem' => 'A senha atual está incorreta.'
            ], 422);
        }

        $usuario->update([
            'senha' => Hash::make($request->nova_senha)
        ]);

        return response()->json([
            'mensagem' => 'Senha alterada com sucesso!'
        ]);
    }

    public function historico()
    {
        // Busca todas as entregas associadas ao entregador ou a empresa que requisitou
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
            // Verifica se vai retornar todas as entregas da empresa ou do entregador
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

        // Esconde ID's que não tem utilidade na listagem
        $historico->makeHidden([
            'endereco_origem_id', 
            'endereco_destino_id',
            'empresa_id',
            'entregador_id'
        ]);

        return response()->json([
            'dados' => $historico
        ]);
    }

}