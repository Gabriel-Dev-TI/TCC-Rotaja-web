<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Entrega;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function empresa()
    {
        $empresa = Empresa::where('usuario_id', Auth::id())->firstOrFail();

        // Total de entregas da empresa
        $totalEntregas = Entrega::where('empresa_id', $empresa->id)->count();

        // Entregas pendentes
        $entregasPendentes = Entrega::where('empresa_id', $empresa->id)
            ->where('status', 'pendente')
            ->count();

        // Entregas em andamento
        $entregasAndamento = Entrega::where('empresa_id', $empresa->id)
            ->whereIn('status', ['aceita', 'em_transito'])
            ->count();

        // Entregas concluídas
        $entregasConcluidas = Entrega::where('empresa_id', $empresa->id)
            ->where('status', 'concluido')
            ->count();

        // Entregas canceladas
        $entregasCanceladas = Entrega::where('empresa_id', $empresa->id)
            ->where('status', 'cancelado')
            ->count();

        // Últimas entregas
        $ultimasEntregas = Entrega::with([
                'empresa.usuario',
                'entregador.usuario'
            ])
            ->where('empresa_id', $empresa->id)
            ->latest()
            ->take(8)
            ->get();

        // Entregas por mês
        $entregasMensais = Entrega::where('empresa_id', $empresa->id)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $dadosMensais = [];

        for ($i = 1; $i <= 12; $i++) {
            $dadosMensais[] = $entregasMensais[$i] ?? 0;
        }

        return view('empresa.dashboard', compact(
            'totalEntregas',
            'entregasPendentes',
            'entregasAndamento',
            'entregasConcluidas',
            'entregasCanceladas',
            'ultimasEntregas',
            'dadosMensais'
        ));
    }
}