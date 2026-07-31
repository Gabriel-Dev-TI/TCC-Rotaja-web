<?php

namespace App\Http\Controllers;

use App\Models\Entregador;
use App\Models\Entrega;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EntregadorController extends Controller
{
    public function create()
    {
        return view('entregadores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'cpf'          => 'required|string|unique:entregadores,cpf',
            'telefone'     => 'required|string',
            'tipo_veiculo' => 'required|string',
            'email'        => 'required|email|unique:usuarios,email',
            'password'     => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'nome'     => $request->nome,
                'email'    => $request->email,
                'senha'    => Hash::make($request->password),
                'telefone' => $request->telefone,
                'cargo'    => 'entregador',
            ]);

            Entregador::create([
                'usuario_id'   => $user->id,
                'nome'         => $request->nome,
                'cpf'          => $request->cpf,
                'telefone'     => $request->telefone,
                'tipo_veiculo' => $request->tipo_veiculo,
            ]);

            Auth::login($user);
        });

        return redirect()->route('entregador.dashboard')->with('success', 'Cadastro realizado com sucesso!');
    }

    /**
     * Exibe o Painel/Dashboard do Entregador
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Busca o registro de entregador do usuário logado
        $entregador = Entregador::where('usuario_id', $user->id)->first();

        // Busca a entrega atual que o entregador está realizando (se houver)
        $entregaAtual = null;
        if ($entregador) {
            $entregaAtual = Entrega::where('entregador_id', $entregador->id)
                ->where('status', 'em_andamento')
                ->first();
        }

        // Busca entregas pendentes disponíveis na região para ele aceitar
        $entregasPendentes = Entrega::where('status', 'pendente')->latest()->get();

        return view('entregadores.dashboard', compact('entregaAtual', 'entregasPendentes'));
    }
}