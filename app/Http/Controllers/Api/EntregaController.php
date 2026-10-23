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

        // Para o entregador: retorna entregas que ele aceitou
        // se não tiver entregas em andamento mostra as entregas pendentes (entregas esperando por entregadores)
        if ($usuario->cargo == 'entregador') {
            if (!$usuario->entregador) {
                return response()->json([
                    'mensagem' => 'Perfil de entregador não encontrado.'
                ], 404);
            }

            $entregadorId = $usuario->entregador->id;

            // Busca a entrega que o entregador aceitou
            $minhas = Entrega::with(['empresa', 'enderecoOrigem', 'enderecoDestino'])
                ->where('entregador_id', $entregadorId)
                ->whereIn('status', ['aceita', 'em_transito'])
                ->latest()
                ->get();

            if ($minhas->isNotEmpty()) {
                return response()->json(['dados' => $minhas]);
            }

            // Se não retornou entrega aceita/em_transito, retorna as entregas pendentes (sem entregadores)
            $pendentes = Entrega::with(['empresa', 'enderecoOrigem', 'enderecoDestino'])
                ->where('status', 'pendente')
                ->latest()
                ->get();

            return response()->json(['dados' => $pendentes]);
        }   

        // Para a empresa: retorna entregas pendentes e em transito
        if ($usuario->cargo == 'empresa') {
            if (!$usuario->empresa) {
                return response()->json([
                    'mensagem' => 'Perfil de empresa não encontrado.'
                ], 404);
            }

            // Busca as entregas feitas pela empresa que não foram concluidas ou canceladas
            $entregas = Entrega::with(['entregador', 'enderecoOrigem', 'enderecoDestino'])
                ->where('empresa_id', $usuario->empresa->id)
                ->whereIn('status', ['pendente', 'aceita','em_transito'])
                ->latest()
                ->get();

            return response()->json([
                'dados' => $entregas
            ]);
        }

        // Se não retornar as entregas do entregador nem da empresa não tem permissão
        return response()->json([
            'mensagem' => 'Usuário não tem permissão para consultar entregas'
        ], 403);
    }

    public function store(Request $request)
    {
        // Verifica os dados para criar a entrega
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
                'mensagem' => $validator->errors()->first()
            ], 422);
        }

        $dados = $validator->validated();
        $usuario = Auth::user();

        if (!$usuario || !$usuario->empresa) {
            return response()->json([
                'mensagem' => 'Usuário não possui uma empresa associada.'
            ], 403);
        }

        $empresa = $usuario->empresa;

        // Valida os endereços 
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
                'mensagem' => 'O endereço de origem não pertence à sua empresa.'
            ], 422);
        }

        if (!$destino) {
            return response()->json([
                'mensagem' => 'O endereço de destino não pertence à sua empresa.'
            ], 422);
        }
        
        if ($origem->id === $destino->id) {
            return response()->json([
                'mensagem' => 'O endereço de origem não pode ser igual ao endereço de destino.'
            ], 422);
        }

        if (
            $origem->latitude === null ||
            $origem->longitude === null ||
            $destino->latitude === null ||
            $destino->longitude === null
        ) {
            return response()->json([
                'mensagem' => 'Algum dos endereços não possui localização válida.'
            ], 422);
        }

         try {
            // Se os endereços e os dados da entrega são validos 
            // Calcula os dados restantes da entrega (distancia,tempo,preco)

            $url =
                'https://api.mapbox.com/directions/v5/mapbox/driving/' .
                $origem->longitude . ',' .
                $origem->latitude . ';' .
                $destino->longitude . ',' .
                $destino->latitude .
                '?overview=false' .
                '&access_token=' . config('services.mapbox.public_token');

            // Espera 10 segundos para a resposta da API
            $response = Http::timeout(10)->get($url);

        } catch (\Throwable $e) {
            return response()->json(['mensagem' => 'Não foi possível calcular a rota.'], 422);
        }

        if (!$response->successful() || $response->json('code') !== 'Ok' || empty($response->json('routes'))) {
            return response()->json(['mensagem' => 'Não foi encontrada uma rota entre os endereços fornecidos.'], 422);
        }

        // Conversão
        $rota = $response->json('routes.0');
        $distanciaKm = $rota['distance'] / 1000;
        $tempoMinutos = round($rota['duration'] / 60);

        //Calculo do preço de acordo com volume,peso e distância
        $volume = ($dados['altura'] * $dados['largura'] * $dados['comprimento']) / 1000000;
        $adicionalPeso = $dados['peso'] * 0.50;
        $adicionalVolume = $volume * 50;
        $precoPorKm = 1.50;

        $preco = ($distanciaKm * $precoPorKm) + $adicionalPeso + $adicionalVolume;

        // Cadastra a entrega
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
                'mensagem' => 'Entrega cadastrada com sucesso!',
                'dados' => $entrega
            ], 201);
        });
    }

    public function show($id)
    {
        
        $usuario = Auth::user();

        // Busca os dados da entrega para mostrar o detalhamento no movel
        $entrega = Entrega::with([
            'empresa',
            'entregador',
            'enderecoOrigem',
            'enderecoDestino'
        ])->findOrFail($id);

        // Formata a data e hora para listar no movel
        $dados = $entrega->toArray();
        $dados['data'] = $entrega->created_at?->format('d/m/Y');
        $dados['hora'] = $entrega->created_at?->format('H:i');

        return response()->json(['dados' => $dados]);
    }   

    public function update(Request $request, $id)
    {
        //--------------------------------
        // ATUALIZA STATUS DA ENTREGA
        //--------------------------------

        $usuario = Auth::user();
        $entrega = Entrega::findOrFail($id);

        // Verifica permissão
        if ($usuario->cargo === 'empresa' && $entrega->empresa_id !== $usuario->empresa->id) {
            return response()->json(['mensagem' => 'Sem permissão.'], 403);
        }
        if ($usuario->cargo === 'entregador' && $entrega->entregador_id !== $usuario->entregador->id && $entrega->status !== 'pendente' ) {
            return response()->json(['mensagem' => 'Sem permissão.'], 403);
        }

        // Se for entregador verifica se existe observacao antes de cancelar ou excluir
        $request->validate([
            'status' => 'required|in:aceita,em_transito,concluido,cancelado',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $novoStatus = $request->status;
        $statusAtual = $entrega->status;

        // Regras de transição
        if ($novoStatus === 'aceita' && $statusAtual !== 'pendente') {
            return response()->json(['mensagem' => 'Só entregas pendentes podem ser aceitas.'], 422);
        }

        if ($novoStatus === 'em_transito' && !in_array($statusAtual, ['pendente', 'aceita'])) {
            return response()->json(['mensagem' => 'Só entregas pendentes ou aceitas podem iniciar.'], 422);
        }

        if ($novoStatus === 'concluido' && $statusAtual !== 'em_transito') {
            return response()->json(['mensagem' => 'Só entregas em trânsito podem ser concluídas.'], 422);
        }

        if ($novoStatus === 'cancelado' && in_array($statusAtual, ['concluido', 'cancelado'])) {
            return response()->json(['mensagem' => 'Esta entrega não pode mais ser cancelada.'], 422);
        }

        $dados = ['status' => $novoStatus];

        if ($novoStatus === 'aceita' || $novoStatus === 'em_transito') {
            $dados['entregador_id'] = $usuario->entregador->id;
        }

        if ($request->filled('observacoes')) {
            $dados['observacoes'] = $request->observacoes;
        }

        $entrega->update($dados);

        // fresh busca a entrega atualizada no banco de dados
        return response()->json([
            'mensagem' => 'Entrega atualizada com sucesso.',
            'dados' => $entrega->fresh(['empresa', 'entregador', 'enderecoOrigem', 'enderecoDestino']),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // Muda o status da entrega para cancelado
        $usuario = Auth::user();
        $entrega = Entrega::findOrFail($id);

        if ($usuario->cargo === 'empresa' && $entrega->empresa_id !== $usuario->empresa->id) {
            return response()->json(['mensagem' => 'Sem permissão.'], 403);
        }
        if ($usuario->cargo === 'entregador' && $entrega->entregador_id !== $usuario->entregador->id) {
            return response()->json(['mensagem' => 'Sem permissão.'], 403);
        }

        if (in_array($entrega->status, ['concluido', 'cancelado'])) {
            return response()->json(['mensagem' => 'Esta entrega não pode mais ser cancelada.'], 422);
        }

        // Se vier observação no body, salva antes de cancelar
        if ($request->filled('observacoes')) {
            $entrega->observacoes = $request->observacoes;
        }

        $entrega->update(['status' => 'cancelado']);

        return response()->json([
            'mensagem' => 'Entrega cancelada com sucesso.',
            'dados' => $entrega,
        ]);
    }   
}