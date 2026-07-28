<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Endereco;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    public function index()
    {
        return response()->json(['sucesso' => true, 'dados' => Empresa::with(['usuario', 'endereco'])->get()]);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        // Dados da Empresa 
        'nome' => 'required|string|max:255',
        'email' => 'required|email|unique:usuarios,email',
        'senha' => 'required|string|min:6',
        'telefone' => 'required|string',
        'cnpj' => 'required|string|unique:empresas,cnpj',
        
        // Validação do Objeto de Endereço aninhado
        'endereco' => 'required|array',
        'endereco.logradouro' => 'required|string|max:255',
        'endereco.numero' => 'required|string|max:10',
        'endereco.bairro' => 'required|string|max:255',
        'endereco.cidade' => 'required|string|max:255',
        'endereco.estado' => 'required|string',
        'endereco.cep' => 'required|string|max:9',
        'endereco.complemento' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'sucesso' => false,
            'mensagem' => $validator->errors()->first()
        ], 422);
    }

    $dados = $validator->validated();

    return DB::transaction(function () use ($dados) {
        
        // Cria o usuario
        $usuario = User::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => Hash::make($dados['senha']),
            'telefone' => $dados['telefone'],
            'cargo' => 'empresa',
        ]);

        // Cria o endereco
        $endereco = Endereco::create($dados['endereco']);

        // Cria a empresa
        $empresa = Empresa::create([
            'cnpj' => $dados['cnpj'],
            'endereco_id' => $endereco->id,
            'usuario_id' => $usuario->id,
        ]);

        $token = $usuario->createToken('flutter')->plainTextToken;

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Empresa cadastrada com sucesso!',
            'token' => $token
        ], 201);
    });
}
    public function show(string $id)
    {
        $empresa = Empresa::with(['usuario', 'endereco'])->findOrFail($id);

        return response()->json(['sucesso' => true, 'dados' => $empresa]);
    }

    public function update(Request $request, string $id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->update($request->all());

        return response()->json(['sucesso' => true, 'dados' => $empresa]);
    }

    public function destroy(string $id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();

        return response()->json(['sucesso' => true, 'mensagem' => 'Empresa removida com sucesso']);
    }
}