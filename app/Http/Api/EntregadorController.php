<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entregador;
use App\Models\Entrega;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EntregadorController extends Controller
{
    public function index()
    {
        return response()->json(['sucesso' => true, 'dados' => Entregador::with('usuario')->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|string|min:6',
            'telefone' => 'required|string',
            'cpf' => 'required|string|unique:entregadores,cpf',
            'tipo_veiculo' => 'required|in:carro,moto,bike,caminhao,outro',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => $validator->errors()->first()
            ], 422);
        }

        $dados = $validator->validated();

        return DB::transaction(function () use ($dados) {
            $usuario = User::create([
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'senha' => Hash::make($dados['senha']),
                'telefone' => $dados['telefone'],
                'cargo' => 'entregador',
            ]);

            $entregador = Entregador::create([
                'cpf' => $dados['cpf'],
                'tipo_veiculo' => $dados['tipo_veiculo'],
                'usuario_id' => $usuario->id,
            ]);

            $token = $usuario->createToken('flutter')->plainTextToken;

            return response()->json([
                'sucesso' => true,
                'token' => $token
            ], 201);
        });
    }

    public function show(string $id)
    {
        $entregador = Entregador::with('usuario')->findOrFail($id);

        return response()->json(['sucesso' => true, 'dados' => $entregador]);
    }

    public function update(Request $request, string $id)
    {
        $entregador = Entregador::findOrFail($id);
        $entregador->update($request->all());

        return response()->json(['sucesso' => true, 'dados' => $entregador]);
    }

    public function destroy(string $id)
    {
        $entregador = Entregador::findOrFail($id);
        $entregador->delete();

        return response()->json(['sucesso' => true, 'mensagem' => 'Entregador removido com sucesso']);
    }

}