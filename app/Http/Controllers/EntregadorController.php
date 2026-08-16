<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EntregadorController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $entregador = $usuario->entregador;

        $entregas = $entregador
            ? $entregador->entregas()
                ->with('empresa.usuario')
                ->latest()
                ->get()
            : collect();

        $entregasDisponiveis = $entregas->where('status', 'pendente');

        $entregasAceitas = $entregas->whereIn('status', [
            'aceita',
            'em_transito',
        ]);

        $entregasConcluidas = $entregas->where('status', 'concluido');

        $totalEntregas = $entregas->count();

        return view('entregador.dashboard', compact(
            'usuario',
            'entregador',
            'entregas',
            'entregasDisponiveis',
            'entregasAceitas',
            'entregasConcluidas',
            'totalEntregas'
        ));
    }
}