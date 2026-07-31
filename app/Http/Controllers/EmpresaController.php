<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    /**
     * Exibe a tela de cadastro da empresa
     */
    public function create()
    {
        return view('empresas.create');
    }

    /**
     * Processa o cadastro da empresa e do usuário
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'cnpj'     => 'required|string|unique:empresas,cnpj',
            'telefone' => 'required|string',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            
            // 1. Cria o usuário associado na tabela 'usuarios'
            $user = User::create([
                'nome'     => $request->nome,
                'email'    => $request->email,
                'senha'    => Hash::make($request->password),
                'telefone' => $request->telefone,
                'cargo'    => 'empresa',
            ]);

            // 2. Cria a empresa associada
            Empresa::create([
                'usuario_id' => $user->id,
                'nome'       => $request->nome,
                'cnpj'       => $request->cnpj,
                'email'      => $request->email,
                'telefone'   => $request->telefone,
            ]);

            // 3. Loga o usuário cadastrado
            Auth::login($user);
        });

        return redirect()->route('empresa.dashboard')->with('success', 'Empresa cadastrada com sucesso!');
    }

    // ... Seus outros métodos (dashboard, configuracoes, etc.)
}