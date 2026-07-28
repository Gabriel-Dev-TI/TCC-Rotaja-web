<?php

namespace App\Http\Controllers;

use App\Models\Entregador;
use App\Models\Entrega;
use Illuminate\Http\Request;

class EntregadorController extends Controller
{
    // Tela pública de cadastro
    public function create()
    {
        return view('entregadores.create');
    }

    // Salvar cadastro do entregador
    public function store(Request $request)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'cpf'          => 'required|string|unique:entregadores',
            'tipo_veiculo' => 'required|string',
            'telefone'     => 'required|string',
        ]);

        Entregador::create($request->all());

        return redirect()->route('login')->with('success', 'Cadastro realizado! Aguarde a aprovação do Admin.');
    }

    // Painel do Entregador: Entregas disponíveis e corrida atual
    public function dashboard()
    {
        $entregadorId = auth()->user()->entregador_id ?? null;

        // Entregas abertas para qualquer entregador aceitar
        $entregasPendentes = Entrega::where('status', 'pendente')->get();

        // Entrega que este entregador aceitou e está fazendo agora
        $entregaAtual = Entrega::where('entregador_id', $entregadorId)
            ->where('status', 'em_andamento')
            ->first();

        return view('entregadores.dashboard', compact('entregasPendentes', 'entregaAtual'));
    }
}