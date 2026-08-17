<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    public function create()
    {
        $usuario = auth()->user();

        $empresa = $usuario->empresa;

        $enderecos = collect();

        if ($empresa->endereco) {
            $enderecos->push($empresa->endereco);
        }

        $outrosEnderecos = Endereco::whereHas('empresas', function ($query) use ($empresa) {

            $query->where('empresas.id', $empresa->id);

        })
        ->where('id', '!=', $empresa->endereco_id)
        ->get();

        $enderecos = $enderecos
            ->merge($outrosEnderecos)
            ->unique('id');

        return view('empresa.entregas', [
            'enderecos' => $enderecos,
        ]);
    }



   public function store(Request $request)
{
    $dados = $request->validate([

        'nome_produto' => [
            'required',
            'string',
            'max:255'
        ],

        'endereco_origem_id' => [
            'required',
            'integer',
            'exists:enderecos,id'
        ],

        'endereco_destino_id' => [
            'required',
            'integer',
            'exists:enderecos,id'
        ],

        'altura' => [
            'required',
            'numeric',
            'min:0'
        ],

        'largura' => [
            'required',
            'numeric',
            'min:0'
        ],

        'comprimento' => [
            'required',
            'numeric',
            'min:0'
        ],

        'peso' => [
            'required',
            'numeric',
            'min:0'
        ],

        'descricao' => [
            'nullable',
            'string'
        ],

    ]);


    $origem = Endereco::findOrFail(
        $dados['endereco_origem_id']
    );

    $destino = Endereco::findOrFail(
        $dados['endereco_destino_id']
    );

    if (
        $origem->latitude === null ||
        $origem->longitude === null ||
        $destino->latitude === null ||
        $destino->longitude === null
    ) {

        return back()
            ->withInput()
            ->withErrors([
                'endereco_origem_id' =>
                    'Um dos endereços não possui localização válida.'
            ]);
    }


    $url =
        'https://router.project-osrm.org/route/v1/driving/' .
        $origem->longitude . ',' .
        $origem->latitude . ';' .
        $destino->longitude . ',' .
        $destino->latitude .
        '?overview=false';


    try {

        $response = Http::timeout(10)
            ->get($url);

    } catch (\Throwable $e) {

        return back()
            ->withInput()
            ->withErrors([
                'endereco_destino_id' =>
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
                'endereco_destino_id' =>
                    'Não foi encontrada uma rota entre os endereços.'
            ]);
    }


    $rota = $response->json('routes.0');
    $distanciaKm =$rota['distance'] / 1000;
    $tempoMinutos =
        round($rota['duration'] / 60);


    //Volume do produto (divide por 1.000.000 para obter m³)
    $volume =
        (
            $dados['altura'] *
            $dados['largura'] *
            $dados['comprimento']
        ) / 1000000;

    $precoPorKm = 1.50;

    // 50 centavos por kilo
    $adicionalPeso = $dados['peso'] * 0.50;

    
    $adicionalVolume = $volume * 50;
    

    $preco =
        ($distanciaKm * $precoPorKm) +
        $adicionalPeso +
        $adicionalVolume;

    $preco = round($preco, 2);

    DB::transaction(function () use (
        $dados,
        $origem,
        $destino,
        $distanciaKm,
        $tempoMinutos,
        $preco
    ) {

        Entrega::create([

            'nome_produto' =>
                $dados['nome_produto'],

            'endereco_origem_id' =>
                $origem->id,

            'endereco_destino_id' =>
                $destino->id,

            'altura' =>
                $dados['altura'],

            'largura' =>
                $dados['largura'],

            'comprimento' =>
                $dados['comprimento'],

            'peso' =>
                $dados['peso'],

            'descricao' =>
                $dados['descricao'] ?? null,

            'distancia' =>
                round($distanciaKm, 2),

            'tempo_estimado' =>
                $tempoMinutos,

            'preco' =>
                $preco,

            'status' =>
                'pendente',

            'empresa_id' =>
                auth()->user()->empresa->id,

        ]);

    });


    return redirect()
        ->route('empresa.dashboard')
        ->with(
            'success',
            'Entrega cadastrada com sucesso!'
        );
}

    public function aceitar($id)
    {
        $entrega = Entrega::findOrFail($id);

        $entregador = auth()->user()->entregador;
        $entrega->update([

            'entregador_id' =>
                $entregador->id,

            'status' =>
                'aceita',

        ]);

        return back()->with(
            'success',
            'Entrega aceita! Inicie a rota no mapa.'
        );
    }



    public function rota()
    {
        $usuario = auth()->user();

        $entregador = $usuario->entregador;

        $entrega = Entrega::with([

            'empresa.usuario',

            'enderecoOrigem',

            'enderecoDestino',

        ])
        ->where(
            'entregador_id',
            $entregador->id
        )
        ->whereIn(
            'status',
            [
                'aceita',
                'em_transito'
            ]
        )
        ->latest()
        ->first();


        return view(
            'entregador.rota',
            [
                'entrega' => $entrega,
            ]
        );
    }

    public function finalizar(Entrega $entrega)
    {
        $entregador =
            auth()->user()->entregador;

        if (!$entregador) {
            abort(403);
        }


        if (
            $entrega->entregador_id !==
            $entregador->id
        ) {
            abort(403);
        }


        if (
            !in_array(
                $entrega->status,
                [
                    'aceita',
                    'em_transito'
                ]
            )
        ) {
            return back()->with(
                'error',
                'Esta entrega não está em andamento.'
            );
        }


        $entrega->update([

            'status' =>
                'concluido',

        ]);


        return redirect()
            ->route('rota')
            ->with(
                'success',
                'Entrega finalizada com sucesso.'
            );
    }

    public function observacao(
        Request $request,
        Entrega $entrega
    ) {

        $entregador =
            auth()->user()->entregador;


        if (!$entregador) {
            abort(403);
        }


        if (
            $entrega->entregador_id !==
            $entregador->id
        ) {
            abort(403);
        }


        $validated =
            $request->validate([

                'observacao' => [
                    'required',
                    'string',
                    'max:1000',
                ],

            ]);


        $entrega->update([

            'observacoes' =>
                $validated['observacao'],

        ]);


        return back()->with(
            'success',
            'Observação registrada com sucesso.'
        );
    }


    public function ocorrencia(
        Request $request,
        Entrega $entrega
    ) {

        $entregador =
            auth()->user()->entregador;


        if (!$entregador) {
            abort(403);
        }


        if (
            $entrega->entregador_id !==
            $entregador->id
        ) {
            abort(403);
        }


        $validated =
            $request->validate([

                'observacoes' => [
                    'required',
                    'string',
                    'max:1000',
                ],

            ]);


        $entrega->update([

            'observacoes' =>
                $validated['observacoes'],

        ]);


        return response()->json([

            'success' => true,

            'message' =>
                'Observação registrada com sucesso.',

        ]);
    }

    public function historico()
    {
        $usuario = auth()->user();


        if ($usuario->cargo === 'empresa') {

            $empresa =
                $usuario->empresa;

            if (!$empresa) {
                abort(403, 'Empresa não encontrada.');
            }


            $ultimasEntregas =
                $empresa
                    ->entregas()
                    ->with([

                        'empresa.usuario',

                        'entregador.usuario',

                        'enderecoOrigem',

                        'enderecoDestino',

                    ])
                    ->latest()
                    ->get();


        } elseif ($usuario->cargo === 'entregador') {

            $entregador =
                $usuario->entregador;

            if (!$entregador) {
                abort(403, 'Entregador não encontrado.');
            }


            $ultimasEntregas =
                $entregador
                    ->entregas()
                    ->with([

                        'empresa.usuario',

                        'entregador.usuario',

                        'enderecoOrigem',

                        'enderecoDestino',

                    ])
                    ->latest()
                    ->get();


        } else {

            $ultimasEntregas =
                collect();

        }


        return view(
            'usuario.historico',
            [
                'ultimasEntregas' =>
                    $ultimasEntregas,
            ]
        );
    }
}