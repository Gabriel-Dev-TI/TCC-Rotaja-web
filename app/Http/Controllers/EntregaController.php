<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    /**
     * Lista o historico de entregas 
     */
    public function index()
    {
        $usuario = auth()->user();
        $cargo = $usuario->cargo;
        $user = $usuario->$cargo;

        if (!$user) {abort(403, ucfirst($cargo) . ' não encontrado.');}

        $ultimasEntregas = $user
            ->entregas()
            ->with([
                'empresa.usuario',
                'entregador.usuario',
                'enderecoOrigem',
                'enderecoDestino',
            ])
            ->latest()
            ->get();

        return view('usuario.historico', [
            'ultimasEntregas' => $ultimasEntregas,
        ]);
    }

    /**
     * Mostra o formulário para criar uma nova entrega 
     * passando os endereços da empresa logada
     */
    public function create()
    {
        $usuario = auth()->user();
        $empresa = $usuario->empresa;

        if (!$empresa) {
            abort(403, 'Empresa não encontrada.');
        }

        $enderecos = $empresa
            ->enderecos()
            ->latest()
            ->get();

        return view('empresa.entregas', [
            'enderecos' => $enderecos,
        ]);
    }

    /**
     * Cadastra uma nova entrega, calculando a rota entre os endereços de origem e destino
     */
    public function store(Request $request)
    {
        $usuario = auth()->user();
        $empresa = $usuario->empresa;

        if (!$empresa) {
        abort(403, 'Empresa não encontrada.');
    }

        $dados = $request->validate([

            'nome_produto' => ['required','string','max:255'],
            'endereco_origem_id' => ['required','integer','exists:enderecos,id'],
            'endereco_destino_id' => ['required','integer','exists:enderecos,id'],
            'altura' => ['required','numeric','min:0'],
            'largura' => ['required','numeric','min:0'],
            'comprimento' => ['required','numeric','min:0'],
            'peso' => ['required','numeric','min:0'],
            'descricao' => ['nullable','string'],

        ]);


        //CALCULO DE ROTA ENTRE ENDEREÇOS

        $origem = $empresa
            ->enderecos()
            ->where('id', $dados['endereco_origem_id'])
            ->first();

        $destino = $empresa
            ->enderecos()
            ->where('id', $dados['endereco_destino_id'])
            ->first();

        if (!$origem) {
            return back()
                ->withInput()
                ->withErrors([
                    'endereco_origem_id' =>
                        'O endereço de origem não pertence à sua empresa.',
                ]);
        }

        if (!$destino) {
            return back()
                ->withInput()
                ->withErrors([
                    'endereco_destino_id' =>
                        'O endereço de destino não pertence à sua empresa.',
                ]);
        }

        if (
            $origem->latitude === null ||
            $origem->longitude === null ||
            $destino->latitude === null ||
            $destino->longitude === null
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'endereco' =>'Algum dos endereços não possui localização válida.'
                ]);
        }

        try {

            $url =
            'https://router.project-osrm.org/route/v1/driving/' .
            $origem->longitude . ',' .
            $origem->latitude . ';' .
            $destino->longitude . ',' .
            $destino->latitude .
            '?overview=false';

            // Espera 10 segundos para a resposta da API
            $response = Http::timeout(10)->get($url);

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'endereco' =>
                        'Não foi possível calcular a rota.'
                ]);
        }

        if (
            !$response->successful() ||
            $response->json('code') !== 'Ok' ||
            empty($response->json('routes'))
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'endereco' =>
                        'Não foi encontrada uma rota entre os endereços.'
                ]);
        }


        // CALCULO DO PREÇO DA ENTREGA
        $rota = $response->json('routes.0');
        $distanciaKm =$rota['distance'] / 1000;
        $tempoMinutos = round($rota['duration'] / 60);


        //Volume do produto 
        $volume = ($dados['altura'] *$dados['largura'] *$dados['comprimento']) / 1000000;

        // 50 centavos por kilo
        $adicionalPeso = $dados['peso'] * 0.50;

        // 50 reais por metro cubico
        $adicionalVolume = $volume * 50;

        // Taxa base de 1,50 por km
        $precoPorKm = 1.50;
        
        $preco = ($distanciaKm * $precoPorKm) + $adicionalPeso + $adicionalVolume;

        DB::transaction(function () use (
            $dados,
            $origem,
            $destino,
            $distanciaKm,
            $tempoMinutos,
            $preco,
            $empresa
        ) {

            Entrega::create([

                'nome_produto' => $dados['nome_produto'],
                'endereco_origem_id' => $origem->id,
                'endereco_destino_id' => $destino->id,
                'altura' => $dados['altura'],
                'largura' => $dados['largura'],
                'comprimento' => $dados['comprimento'],
                'peso' => $dados['peso'],
                'descricao' => $dados['descricao'] ?? null,
                'distancia' => round($distanciaKm, 2),
                'tempo_estimado_minutos' => $tempoMinutos,
                'preco' => round($preco, 2),
                'status' => 'pendente',
                'empresa_id' => $empresa->id,

            ]);

        });


        return redirect()
            ->route('empresa.dashboard')
            ->with(
                'success',
                'Entrega cadastrada com sucesso!'
            );
    }

    /**
    * Exibe os detalhes de uma entrega.
    */
    public function show(Entrega $entrega)
    {
        $usuario = auth()->user();
        $cargo = $usuario->cargo;
        $user = $usuario->$cargo;

        if (!$user && $cargo !== 'admin') {
            abort(403, ucfirst($cargo) . ' não encontrado.');
        }

        if ($cargo !== 'admin') {

            $campoId = $cargo . '_id';

            if ($entrega->$campoId !== $user->id) {
                abort(403, 'Você não tem acesso a esta entrega.');
            }
        }

        $entrega->load([
            'empresa.usuario',
            'entregador.usuario',
            'enderecoOrigem',
            'enderecoDestino',
        ]);

        return view('entregas.show', ['entrega' => $entrega,]);
    }


    /**
     * Mostra o formulário de edição.
     */
    public function edit(Entrega $entrega)
    {
        $usuario = auth()->user();

        $cargo = $usuario->cargo;
        $user = $usuario->$cargo;

        if (!$user && $cargo !== 'admin') {
            abort(403, ucfirst($cargo) . ' não encontrado.');
        }

        if ($cargo !== 'admin') {

            $campoId = $cargo . '_id';

            if ($entrega->$campoId !== $user->id) {
                abort(403, 'Você não tem acesso a esta entrega.');
            }
        }

        if ($cargo === 'entregador') {
            abort(403, 'Entregador não pode editar entrega.');
        }

        $entrega->load([
            'empresa.usuario',
            'entregador.usuario',
            'enderecoOrigem',
            'enderecoDestino',
        ]);

        return view('entregas.edit', [
            'entrega' => $entrega,
        ]);
    }


    /**
     * Atualiza uma entrega.
     */
    public function update(Request $request, Entrega $entrega)
    {
        $usuario = auth()->user();
        $cargo = $usuario->cargo;
        $user = $usuario->$cargo;

        if (!$user && $cargo !== 'admin') {
            abort(403, ucfirst($cargo) . ' não encontrado.');
        }

        if ($cargo !== 'admin') {

            $campoId = $cargo . '_id';

            if ($entrega->$campoId !== $user->id) {
                abort(403, 'Você não tem acesso a esta entrega.');
            }
        }

        $dados = $request->validate([
            'nome_produto' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
        ]);

        $entrega->update($dados);

        return redirect()
            ->route('entregas.show', $entrega)
            ->with('success', 'Entrega atualizada com sucesso.');
    }


    /**
     * Remove uma entrega.
     */
    public function destroy(Entrega $entrega)
    {
        $usuario = auth()->user();

        $cargo = $usuario->cargo;
        $user = $usuario->$cargo;

        if (!$user && $cargo !== 'admin') {
            abort(403, ucfirst($cargo) . ' não encontrado.');
        }

        if ($cargo !== 'admin') {

            $campoId = $cargo . '_id';

            if ($entrega->$campoId !== $user->id) {
                abort(403, 'Você não tem acesso a esta entrega.');
            }
        }

        $entrega->delete();

        return redirect()
            ->route('entregas.index')
            ->with('success', 'Entrega excluída com sucesso.');
    }
}
