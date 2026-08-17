<?php

namespace App\Http\Controllers;

use App\Models\Entrega;

class EntregadorController extends Controller
{
    public function index()
    {
        $entregador = auth()->user()->entregador;

        //entregas disponiveis
        $entregas = Entrega::with([
            'empresa.usuario',
            'enderecoOrigem',
            'enderecoDestino',
            'entregador'
        ])
        ->where(function ($query) use ($entregador) {

            $query->where('status', 'pendente')

                ->orWhere(function ($query) use ($entregador) {

                    $query->whereIn('status', [
                        'aceita',
                        'em_transito'
                    ])
                    ->where(
                        'entregador_id',
                        $entregador->id
                    );

                });

        })
        ->latest()
        ->get();

        //Entregas para o grafico
        $entregasConcluidas = Entrega::where(
            'entregador_id',
            $entregador->id
        )
        ->where('status', 'concluido')
        ->get();

        $dadosMensais = array_fill(0, 12, 0);

        foreach ($entregasConcluidas as $entrega) {

            if (!$entrega->updated_at) {
                continue;
            }

            $mes = $entrega->updated_at->month;

            $dadosMensais[$mes - 1]++;
        }


        return view(
            'entregador.dashboard',
            compact(
                'entregas',
                'dadosMensais'
            )
        );
    }


    public function aceitarEntrega($id)
    {
        $entregador = auth()->user()->entregador;

        $entrega = Entrega::where('id', $id)
            ->where('status', 'pendente')
            ->first();


        if (!$entrega) {

            return redirect()
                ->back()
                ->withErrors([
                    'entrega' =>
                        'Esta entrega não está mais disponível.'
                ]);
        }

        $atualizada = Entrega::where('id', $entrega->id)
            ->where('status', 'pendente')
            ->update([

                'entregador_id' => $entregador->id,

                'status' => 'aceita',

                'updated_at' => now(),

            ]);

        if ($atualizada === 0) {

            return redirect()
                ->back()
                ->withErrors([
                    'entrega' =>
                        'Esta entrega acabou de ser aceita por outro entregador.'
                ]);
        }


        return redirect()
            ->back()
            ->with(
                'success',
                'Entrega aceita com sucesso!'
            );
    }
}