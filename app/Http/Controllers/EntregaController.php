<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    // Criar nova entrega (Ação da Empresa)
    public function store(Request $request)
    {
        $request->validate([
            'endereco_origem'  => 'required|string',
            'endereco_destino' => 'required|string',
            'valor'            => 'required|numeric',
        ]);

        Entrega::create([
            'empresa_id'       => auth()->user()->empresa_id,
            'endereco_origem'  => $request->endereco_origem,
            'endereco_destino' => $request->endereco_destino,
            'valor'            => $request->valor,
            'status'           => 'pendente',
        ]);

        return back()->with('success', 'Pedido de entrega criado com sucesso!');
    }

    // Aceitar entrega (Ação do Entregador)
    public function aceitar($id)
    {
        $entrega = Entrega::findOrFail($id);
        
        $entrega->update([
            'entregador_id' => auth()->user()->entregador_id,
            'status'        => 'em_andamento',
        ]);

        return back()->with('success', 'Entrega aceita! Inicie a rota no mapa.');
    }

    // Finalizar entrega com observação (Ação do Entregador)
    public function finalizar(Request $request, $id)
    {
        $request->validate([
            'observacao' => 'nullable|string|max:500',
        ]);

        $entrega = Entrega::findOrFail($id);

        $entrega->update([
            'status'     => 'concluida',
            'observacao' => $request->observacao,
        ]);

        return back()->with('success', 'Entrega finalizada com sucesso!');
    }

    // Histórico de entregas (Geral)
    public function historico()
    {
        $user = auth()->user();

        $query = Entrega::query();

        if ($user->empresa_id) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($user->entregador_id) {
            $query->where('entregador_id', $user->entregador_id);
        }

        $entregas = $query->whereIn('status', ['concluida', 'cancelada'])->latest()->get();

        return view('entregas.historico', compact('entregas'));
    }
}