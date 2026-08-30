<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Entrega;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Empresa::where('usuario_id', Auth::id())->firstOrFail();

        $totalEntregas = Entrega::where('empresa_id', $empresa->id)->count();

        $entregasPendentes = Entrega::where('empresa_id', $empresa->id)
            ->where('status', 'pendente')
            ->count();

        $entregasAndamento = Entrega::where('empresa_id', $empresa->id)
            ->whereIn('status', ['aceita', 'em_transito'])
            ->count();

        $entregasConcluidas = Entrega::where('empresa_id', $empresa->id)
            ->where('status', 'concluido')
            ->count();

        $ultimasEntregas = Entrega::with([
                'empresa.usuario',
                'entregador.usuario'
            ])
            ->where('empresa_id', $empresa->id)
            ->whereIn('status', ['pendente','aceita', 'em_transito'])
            ->latest()
            ->get();

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
            'ultimasEntregas',
            'dadosMensais'
        ));
    }
}