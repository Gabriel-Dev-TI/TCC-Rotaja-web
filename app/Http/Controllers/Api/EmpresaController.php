<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Entrega;
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
        // Valida os dados do endereço para o cadastro
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

        // Se a validação falhar retorna a mensagem de erro para ser listada no movel
        if ($validator->fails()) {
            return response()->json([
                'mensagem' => $validator->errors()->first()
            ], 422);
        }

        $dados = $validator->validated();

        // Pega a empresa autenticada
        $usuario = Auth::user();
        $empresa = $usuario->empresa;
        

        // Cria o endereço atribuindo a empresa como criadora do endereço de entrega
        $endereco = DB::transaction(function () use ($dados, $empresa) {
            $dadosEndereco = array_merge($dados, [
                'empresa_id' => $empresa->id,
                'tipo'       => 'entrega',
            ]);

            return Endereco::create($dadosEndereco);
        });

        return response()->json(['dados' => $endereco], 201);
    }

    public function atualizarEndereco(Request $request, string $id)
    {

        // Valida os dados do endereço para o atualizar
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

        // Se a validação falhar retorna a mensagem de erro para ser listada no movel
        if ($validator->fails()) {
            return response()->json([
                'mensagem' => $validator->errors()->first()
            ], 422);
        }

        // Pega a empresa autenticada
        $usuario = Auth::user();
        $empresa = $usuario->empresa;

        // Encontra o endereço a ser editado pelo id informado
        $endereco = Endereco::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->first();

        // Endereço não encontrado ou não pertence à empresa
        if (!$endereco) {
            return response()->json([
                'mensagem' => 'Endereço não encontrado.'
            ], 404);
        }

        // Verifica se endereço está em uso em alguma entrega
        $pertence = Entrega::where('empresa_id', $empresa->id)
            ->where(function ($query) use ($endereco) {
                $query
                    ->where('endereco_origem_id', $endereco->id)
                    ->orWhere('endereco_destino_id', $endereco->id);
            })
            ->exists();

        if ($pertence) {
            return response()->json([
                'mensagem' => 'O endereço está sendo usado em uma entrega.'
            ], 409);
        }

        // Se passar das verificações atualiza o endereço
        $endereco->update($validator->validated());

        return response()->json([
            'dados'   => $endereco,
        ]);
    }   

    public function deletarEndereco(string $id)
    {
        // Pega a empresa autenticada
        $usuario = Auth::user();
        $empresa = $usuario->empresa;

        // Pega o endereço pelo id e verifica se é da empresa
        $endereco = Endereco::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        // Verifica se o endereço pertence a alguma entrega 
        $pertence = Entrega::where('empresa_id', $empresa->id)
            ->where(function ($query) use ($endereco) {
                $query
                    ->where('endereco_origem_id', $endereco->id)
                    ->orWhere('endereco_destino_id', $endereco->id);
            })
            ->exists();

        // Se o endereço foi usado em uma entrega usa o softdelete
        if ($pertence) {
            $endereco->delete();
        }
        // Se não foi usado ainda, pode apagar totalmente
        else{
            $endereco->forceDelete();
        }

        return response()->json([
            'mensagem' => 'Endereço removido com sucesso',
        ]);
    }   

    public function store(Request $request)
    {
        // Valida a empresa e os dados do endereço a serem cadastrados
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
            'endereco.numero' => 'required|string',
            'endereco.bairro' => 'required|string|max:255',
            'endereco.cidade' => 'required|string|max:255',
            'endereco.estado' => 'required|string',
            'endereco.cep' => 'required|string|max:9',
            'endereco.complemento' => 'nullable|string|max:255',
            'endereco.latitude'    => 'nullable|numeric',
            'endereco.longitude'   => 'nullable|numeric',
        ]);

        // Se a validação falhar retorna a mensagem de erro para ser listada no movel
        if ($validator->fails()) {
            return response()->json([
                'mensagem' => $validator->errors()->first()
            ], 422);
        }

        $dados = $validator->validated();

        // Depois de validados tenta criar o usuario a empresa e o endereço
        // Se algum falhar a criação cancela todos
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

            // Adiciona os campos que faltam para criar o endereço
            $dadosEndereco = array_merge($dados['endereco'], [
                'empresa_id' => $empresa->id,
                'tipo' => 'proprio',
            ]);

            // Cria o endereço 
            $endereco = Endereco::create($dadosEndereco);

            // Cria token para proximas requisições autenticadas
            $usuario->tokens()->delete();
            $token = $usuario->createToken('flutter')->plainTextToken;

            $usuario->load('empresa.enderecos');

            // Retorna os dados para salvar no movel
            return response()->json([
                'token' => $token,
                'usuario' => $usuario,
            ], 201);
        });
    }
}