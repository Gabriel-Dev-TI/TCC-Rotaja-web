<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class EntregaController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        // Para o entregador: retorna entregas pendentes e as que ele aceitou
        if ($usuario->cargo == 'entregador') {
            if (!$usuario->entregador) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Perfil de entregador não encontrado.'
                ], 404);
            }

            $entregadorId = $usuario->entregador->id;

            $entregas = Entrega::with(['empresa', 'enderecoOrigem', 'enderecoDestino'])
                ->where(function ($query) use ($entregadorId) {
                    $query->where('status', 'pendente')
                          ->orWhere(function ($q) use ($entregadorId) {
                              $q->where('entregador_id', $entregadorId)
                                ->where('status', 'em_transito');
                          });
                })
                ->latest()
                ->get();

            return response()->json([
                'sucesso' => true,
                'dados' => $entregas
            ]);
        }

        // Para a empresa: retorna entregas pendentes e em transito
        if ($usuario->cargo == 'empresa') {
            if (!$usuario->empresa) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Perfil de empresa não encontrado.'
                ], 404);
            }

            $entregas = Entrega::with(['entregador', 'enderecoOrigem', 'enderecoDestino'])
                ->where('empresa_id', $usuario->empresa->id)
                ->whereIn('status', ['pendente', 'aceita','em_transito'])
                ->latest()
                ->get();

            return response()->json([
                'sucesso' => true,
                'dados' => $entregas
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Usuário não tem permissão para consultar entregas'
        ], 403);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome_produto' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'largura' => 'required|numeric|min:0|max:1000',
            'altura' => 'required|numeric|min:0|max:1000',
            'peso' => 'required|numeric|min:0|max:1000',
            'comprimento' => 'required|numeric|min:0|max:1000',
            'origem' => 'required|integer|exists:enderecos,id',
            'destino' => 'required|integer|exists:enderecos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => $validator->errors()->first()
            ], 422);
        }

        $dados = $validator->validated();
        $usuario = Auth::user();

        if (!$usuario || !$usuario->empresa) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Usuário não possui uma empresa associada.'
            ], 403);
        }

        $empresa = $usuario->empresa;

        // CALCULO DE ROTA ENTRE ENDEREÇOS
        $origem = $empresa
            ->enderecos()
            ->where('id', $dados['origem'])
            ->first();

        $destino = $empresa
            ->enderecos()
            ->where('id', $dados['destino'])
            ->first();

        if (!$origem) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'O endereço de origem não pertence à sua empresa.'
            ], 422);
        }

        if (!$destino) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'O endereço de destino não pertence à sua empresa.'
            ], 422);
        }

        if (
            $origem->latitude === null ||
            $origem->longitude === null ||
            $destino->latitude === null ||
            $destino->longitude === null
        ) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Algum dos endereços não possui localização válida.'
            ], 422);
        }

        try {
            $url = 'https://router.project-osrm.org/route/v1/driving/' .
                $origem->longitude . ',' .
                $origem->latitude . ';' .
                $destino->longitude . ',' .
                $destino->latitude .
                '?overview=false';

            $response = Http::timeout(10)->get($url);
        } catch (\Throwable $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Não foi possível calcular a rota via API OSRM.'
            ], 500);
        }

        if (
            !$response->successful() ||
            $response->json('code') !== 'Ok' ||
            empty($response->json('routes'))
        ) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Não foi encontrada uma rota entre os endereços fornecidos.'
            ], 422);
        }

        // CALCULO DO PREÇO DA ENTREGA
        $rota = $response->json('routes.0');
        $distanciaKm = $rota['distance'] / 1000;
        $tempoMinutos = round($rota['duration'] / 60);

        $volume = ($dados['altura'] * $dados['largura'] * $dados['comprimento']) / 1000000;

        $adicionalPeso = $dados['peso'] * 0.50;
        $adicionalVolume = $volume * 50;
        $precoPorKm = 1.50;

        $preco = ($distanciaKm * $precoPorKm) + $adicionalPeso + $adicionalVolume;

        return DB::transaction(function () use (
            $dados,
            $empresa,
            $distanciaKm,
            $tempoMinutos,
            $preco
        ) {
            $entrega = Entrega::create([
                'empresa_id' => $empresa->id,
                'nome_produto' => $dados['nome_produto'],
                'descricao' => $dados['descricao'],
                'endereco_origem_id' => $dados['origem'],
                'endereco_destino_id' => $dados['destino'],
                'largura' => $dados['largura'],
                'comprimento' => $dados['comprimento'],
                'altura' => $dados['altura'],
                'peso' => $dados['peso'],
                'distancia' => round($distanciaKm, 2),
                'tempo_estimado_minutos' => $tempoMinutos,
                'preco' => round($preco, 2),
                'status' => 'pendente',
            ]);

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Entrega cadastrada com sucesso!',
                'dados' => $entrega
            ], 201);
        });
    }

    public function show($id)
    {
        $entrega = Entrega::with(['empresa', 'entregador', 'enderecoOrigem', 'enderecoDestino'])->findOrFail($id);

        return response()->json(['sucesso' => true, 'dados' => $entrega]);
    }

    public function update(Request $request, $id)
    {
        $entrega = Entrega::findOrFail($id);
        $entrega->update($request->all());

        return response()->json(['sucesso' => true, 'dados' => $entrega]);
    }

    public function destroy($id)
    {
        $entrega = Entrega::findOrFail($id);
        $entrega->delete();

        return response()->json(['sucesso' => true, 'mensagem' => 'Entrega removida com sucesso']);
    }
}