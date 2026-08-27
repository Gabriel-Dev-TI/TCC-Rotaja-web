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
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function endereco(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logradouro'  => 'required|string|max:255',
            'numero'      => 'required|string',
            'bairro'      => 'required|string|max:255',
            'cidade'      => 'required|string|max:255',
            'estado'      => 'required|string',
            'cep'         => 'required|string|max:9',
            'complemento' => 'nullable|string|max:255',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => $validator->errors()->first()
            ], 422);
        }

        $dados = $validator->validated();
        $usuario = Auth::user();
        $empresa = $usuario->empresa;
        
        $endereco = DB::transaction(function () use ($dados, $empresa) {
            $dadosEndereco = array_merge($dados, [
                'empresa_id' => $empresa->id,
                'tipo'       => 'entrega',
            ]);

            return Endereco::create($dadosEndereco);
        });

        return response()->json(['sucesso' => true, 'dados' => $endereco], 201);
    }

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
        'senha' => 'required|string|min:8',
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
        'endereco.latitude'    => 'nullable|numeric',
        'endereco.longitude'   => 'nullable|numeric',
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

        // Cria a empresa
        $empresa = Empresa::create([
            'cnpj' => $dados['cnpj'],
            'usuario_id' => $usuario->id,
        ]);

        $dadosEndereco = array_merge($dados['endereco'], [
            'empresa_id' => $empresa->id,
            'tipo' => 'proprio',
        ]);

        // Cria o endereço 
        $endereco = Endereco::create($dadosEndereco);

        $token = $usuario->createToken('flutter')->plainTextToken;

        $usuario->load('empresa.enderecos');

        return response()->json([
            'sucesso' => true,
            'token' => $token,
            'usuario' => $usuario,
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