<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Endereco;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CADASTRAR ENTREGA
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $usuario = auth()->user();

        $empresa = $usuario->empresa;

        if (!$empresa) {
            abort(403, 'Empresa não encontrada.');
        }

        $enderecos = collect();

        /*
         * Endereço principal da empresa
         */
        if ($empresa->endereco) {
            $enderecos->push($empresa->endereco);
        }

        /*
         * Outros endereços relacionados à empresa
         */
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


    /*
    |--------------------------------------------------------------------------
    | SALVAR ENTREGA
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $usuario = auth()->user();

        $empresa = $usuario->empresa;

        if (!$empresa) {
            abort(403, 'Empresa não encontrada.');
        }


        $validated = $request->validate([

            'nome_produto' => [
                'required',
                'string',
                'max:255',
            ],

            'descricao' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'preco' => [
                'required',
                'numeric',
                'min:0',
            ],

            'altura' => [
                'required',
                'numeric',
                'min:0',
            ],

            'largura' => [
                'required',
                'numeric',
                'min:0',
            ],

            'comprimento' => [
                'required',
                'numeric',
                'min:0',
            ],

            'peso' => [
                'required',
                'numeric',
                'min:0',
            ],

            'endereco_origem_id' => [
                'required',
                'exists:enderecos,id',
            ],

            'endereco_destino_id' => [
                'required',
                'exists:enderecos,id',
                'different:endereco_origem_id',
            ],

        ]);


        /*
         * Verifica se os endereços pertencem à empresa.
         */

        $enderecosPermitidos = Endereco::where(function ($query) use ($empresa) {

            $query->where(
                'id',
                $empresa->endereco_id
            );

            $query->orWhereHas(
                'empresas',
                function ($query) use ($empresa) {

                    $query->where(
                        'empresas.id',
                        $empresa->id
                    );

                }
            );

        })
        ->pluck('id');


        if (
            !$enderecosPermitidos->contains(
                (int) $validated['endereco_origem_id']
            )
            ||
            !$enderecosPermitidos->contains(
                (int) $validated['endereco_destino_id']
            )
        ) {

            return back()
                ->withErrors([
                    'endereco_origem_id' =>
                        'Um dos endereços selecionados não pertence à empresa.'
                ])
                ->withInput();
        }


        /*
         * Cria a entrega
         */

        Entrega::create([

            'empresa_id' => $empresa->id,

            'nome_produto' =>
                $validated['nome_produto'],

            'descricao' =>
                $validated['descricao'] ?? null,

            'preco' =>
                $validated['preco'],

            'altura' =>
                $validated['altura'],

            'largura' =>
                $validated['largura'],

            'comprimento' =>
                $validated['comprimento'],

            'peso' =>
                $validated['peso'],

            'endereco_origem_id' =>
                $validated['endereco_origem_id'],

            'endereco_destino_id' =>
                $validated['endereco_destino_id'],

            'status' =>
                'pendente',

        ]);


        return redirect()
            ->route('empresa.dashboard')
            ->with(
                'success',
                'Entrega cadastrada com sucesso!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACEITAR ENTREGA
    |--------------------------------------------------------------------------
    */

    public function aceitar($id)
    {
        $entrega = Entrega::findOrFail($id);

        $entregador = auth()->user()->entregador;

        if (!$entregador) {
            abort(403, 'Entregador não encontrado.');
        }

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


    /*
    |--------------------------------------------------------------------------
    | ROTA
    |--------------------------------------------------------------------------
    */

    public function rota()
    {
        $usuario = auth()->user();

        $entregador = $usuario->entregador;

        if (!$entregador) {
            abort(403, 'Entregador não encontrado.');
        }


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


    /*
    |--------------------------------------------------------------------------
    | FINALIZAR
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | OBSERVAÇÃO
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | OCORRÊNCIA
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | HISTÓRICO
    |--------------------------------------------------------------------------
    */

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