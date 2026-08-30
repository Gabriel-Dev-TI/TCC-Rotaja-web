<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use Illuminate\Http\Request;

class EntregadorController extends Controller
{
    public function index()
    {
        $entregador = auth()->user()->entregador;

        // Entregas disponiveis
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

        // Cria um array com 12 elementos (meses) com valor 0
        $dadosMensais = array_fill(0, 12, 0);

        foreach ($entregasConcluidas as $entrega) {

            // Pega as entregas pelo mes em que foi atualizada
            $mes = $entrega->updated_at->month;

            // Adiciona a quatidade de entregas em cada mês
            $dadosMensais[$mes - 1]++;
        }


        //Passa as entregas disponiveis para aceitar e os dados por mes para o grafico
        return view('entregador.dashboard',
            compact(
                'entregas',
                'dadosMensais'
            )
        );
    }


    public function aceitarEntrega($id)
    {
        $entregador = auth()->user()->entregador;

        // Verifica se o entregador já possui uma entrega em andamento
        $entregaEmAndamento = Entrega::where('entregador_id',$entregador->id)
        ->whereIn('status', ['aceita','em_transito'])
        ->exists();

        if ($entregaEmAndamento) {
            return redirect()
                ->back()
                ->withErrors([
                    'entrega' =>
                        'Você já possui uma entrega em andamento. Finalize ou cancele a entrega atual antes de aceitar outra.'
                ]);
        }

        // Consulta as entregas pendentes
        $entrega = Entrega::where('id', $id)
            ->where('status', 'pendente')
            ->first();


        if (!$entrega) {
            return redirect()
                ->back()
                ->withErrors(['entrega' =>'Esta entrega não está mais disponível.']);
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
                ->withErrors(['entrega' =>'Esta entrega acabou de ser aceita por outro entregador.']);
        }


        return redirect()
            ->back()
            ->with('success','Entrega aceita com sucesso!');
    }


     public function rota()
    {
        $usuario = auth()->user();

        $entregador = $usuario->entregador;

        $entrega = Entrega::with(['empresa.usuario','enderecoOrigem','enderecoDestino',])
        ->where('entregador_id',$entregador->id)
        ->whereIn('status',['aceita','em_transito'])
        ->latest()
        ->first();

        return view('entregador.rota',['entrega' => $entrega,]);
    }

    public function finalizar(Entrega $entrega)
    {
        $entregador = auth()->user()->entregador;

        if (!$entregador || $entrega->entregador_id !== $entregador->id) {
            abort(403);
        }

        if (!in_array($entrega->status,['aceita','em_transito'])) {
            return back()->with(
                'error',
                'Esta entrega não está em andamento.'
            );
        }

        $entrega->update(['status' =>'concluido',]);


        return redirect()
            ->route('rota')
            ->with(
                'success',
                'Entrega finalizada com sucesso.'
            );
    }

    public function observacao(Request $request,Entrega $entrega) {

        $entregador = auth()->user()->entregador;

        if (!$entregador || $entrega->entregador_id !== $entregador->id) {
            abort(403);
        }

        $validated = $request->validate(['observacoes' => ['required','string','max:1000',],]);

        $entrega->update(['observacoes' => $validated['observacoes'],]);

        return back()->with('success','Observação registrada com sucesso.');
    }


    public function cancelar(Request $request, Entrega $entrega)
    {
        $entregador = auth()->user()->entregador;

        if (!$entregador || $entrega->entregador_id !== $entregador->id) {
            abort(403);
        }

        if (!in_array($entrega->status, ['aceita', 'em_transito'])) {
            return back()->with(
                'error',
                'Esta entrega não pode ser cancelada.'
            );
        }

        $validated = $request->validate([
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ]);

        $entrega->update([
            'observacoes' => $validated['observacoes'] ?? $entrega->observacoes,
            'status' => 'cancelado',
        ]);

        return redirect()
            ->route('rota')
            ->with('success', 'Entrega cancelada com sucesso.');
    }

}