<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Entrega;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    // Tela pública de cadastro
    public function create()
    {
        return view('empresas.create');
    }

    // Salvar cadastro de empresa
    public function store(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'cnpj'     => 'required|string|unique:empresas',
            'email'    => 'required|email|unique:empresas',
            'telefone' => 'required|string',
        ]);

        Empresa::create($request->only(['nome', 'cnpj', 'email', 'telefone']));

        return redirect()->route('login')->with('success', 'Empresa cadastrada com sucesso! Faça login.');
    }

    // Painel da Empresa: Ver entregas em andamento e formulário
    public function dashboard()
    {
        $empresaId = auth()->user()->empresa_id ?? null;
        
        $entregasEmAndamento = Entrega::where('empresa_id', $empresaId)
            ->whereIn('status', ['pendente', 'em_andamento'])
            ->latest()
            ->get();

        return view('empresas.dashboard', compact('entregasEmAndamento'));
    }

    // Configurações e endereços da empresa
    public function configuracoes()
    {
        $user = auth()->user();
        return view('perfil.configuracoes', compact('user'));
    }
}