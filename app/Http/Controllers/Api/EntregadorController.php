<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entregador;
use App\Models\Entrega;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;   

class EntregadorController extends Controller
{

    public function store(Request $request)
    {
        // Valida os dados do entregador para criar a conta
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|string|min:8',
            'telefone' => 'required|string',
            'cpf' => 'required|string|unique:entregadores,cpf',
            'tipo_veiculo' => 'required|in:carro,moto,bike,caminhao,outro',
        ]);

        if ($validator->fails()) {
            return response()->json(['mensagem' => $validator->errors()->first()], 422);
        }

        $dados = $validator->validated();

        // Cria o usuario e em seguida o entregador deste usuario
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

            // Cria token para proximas requisições autenticadas
            $usuario->tokens()->delete();
            $token = $usuario->createToken('flutter')->plainTextToken;
            
            $usuario->load('entregador');

            return response()->json([
                'token' => $token,
                'usuario' => $usuario,
            ], 201);
        });
    }

    public function ganhos()
    {
        $usuario = Auth::user();
        if ($usuario->cargo != 'entregador' || !$usuario->entregador) {
            return response()->json([
            'mensagem' => 'Usuário não tem permissão para consultar ganhos'
        ], 403);
        }

        // Para o entregador retorna o total de ganhos (soma do preco das entregas que foram concluidas) para listar
        $totalGanhosEntregador = 0;

        $totalGanhosEntregador = Entrega::where('entregador_id', $usuario->entregador->id)
                ->where('status', 'concluido')
                ->whereNull('deleted_at')
                ->sum('preco');
        
        $ganhos = $totalGanhosEntregador;

        return response()->json([
            'ganhos' => $ganhos,
        ], 200);
    }

    public function mapa()
    {
        $usuario = Auth::user();
        if (!$usuario || $usuario->cargo != 'entregador' || !$usuario->entregador) {
           return response()->json(['mensagem' => 'Usuário não tem permissão'], 403);
        }

        $entrega = Entrega::with(['empresa', 'enderecoOrigem', 'enderecoDestino'])
            ->where('entregador_id', $usuario->entregador->id)
            ->whereIn('status', ['aceita', 'em_transito'])
            ->latest()
            ->first();

        if (!$entrega) {
            return response()->json(['mensagem' => 'Entregador não possui entregas aceitas'], 404);
        }

        if (!$entrega->enderecoOrigem || !$entrega->enderecoDestino) {
            return response()->json(['mensagem' => 'Endereço de origem ou destino não configurado.'], 422);
        }   

        try {
            $url = 'https://api.mapbox.com/directions/v5/mapbox/driving/'
                . $entrega->enderecoOrigem->longitude . ',' . $entrega->enderecoOrigem->latitude
                . ';'
                . $entrega->enderecoDestino->longitude . ',' . $entrega->enderecoDestino->latitude
                . '?geometries=geojson&overview=full&steps=false&access_token='
                . config('services.mapbox.public_token');

            $response = Http::timeout(15)->get($url);
        } catch (\Throwable $e) {
            return response()->json(['mensagem' => 'Não foi possível calcular a rota.'], 422);
        }

        if (!$response->successful() || $response->json('code') !== 'Ok' || empty($response->json('routes'))) {
            return response()->json(['mensagem' => 'Não foi encontrada uma rota entre os endereços fornecidos.'], 422);
        }

        $rota = $response->json('routes.0');

        return response()->json([
            'rota'    => $rota,
            'entrega' => $entrega,
            'token'   => config('services.mapbox.public_token'),
        ]);
    }   
}