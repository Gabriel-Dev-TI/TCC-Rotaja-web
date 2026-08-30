<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Entregador;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function index()
    {
        $totalEntregas = Entrega::count();
        $totalEmpresas = Empresa::count();
        $totalEntregadores = Entregador::count();

        $ultimasEntregas = Entrega::with([
            'empresa.usuario',
            'entregador.usuario',
            'enderecoOrigem',
            'enderecoDestino',
        ])
            ->latest()
            ->take(8)
            ->get();

        $anoAtual = now()->year;

        $entregasPorMes = Entrega::select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $anoAtual)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $entregasMensais = [];

        for ($mes = 1; $mes <= 12; $mes++) {
            $entregasMensais[] = $entregasPorMes->get($mes, 0);
        }

        $inicioPeriodo = now()->subMonths(11)->startOfMonth();

        $movimentacoes = Entrega::select(
                DB::raw('YEAR(created_at) as ano'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', $inicioPeriodo)
            ->groupBy(
                DB::raw('YEAR(created_at)'),
                DB::raw('MONTH(created_at)')
            )
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();

        $labelsMovimentacoes = [];
        $dadosMovimentacoes = [];

        for ($i = 11; $i >= 0; $i--) {

            $data = now()
                ->subMonths($i)
                ->startOfMonth();

            $ano = $data->year;
            $mes = $data->month;

            $labelsMovimentacoes[] = $data->translatedFormat('M');

            $registro = $movimentacoes->first(function ($item) use ($ano, $mes) {
                return $item->ano == $ano && $item->mes == $mes;
            });

            $dadosMovimentacoes[] = $registro
                ? $registro->total
                : 0;
        }

        return view('admin.dashboard', [
            'totalEntregas' => $totalEntregas,
            'totalEmpresas' => $totalEmpresas,
            'totalEntregadores' => $totalEntregadores,
            'ultimasEntregas' => $ultimasEntregas,
            'entregasMensais' => $entregasMensais,
            'labelsMovimentacoes' => $labelsMovimentacoes,
            'dadosMovimentacoes' => $dadosMovimentacoes,
        ]);
    }


    public function entregadores()
    {
        $entregadores = Entregador::with('usuario')->latest()->get();
        return view('admin.entregador', compact('entregadores'));
    }


    public function empresas()
    {
        $empresas = Empresa::with(['usuario','enderecos',])->latest()->get();
        return view('admin.empresa', compact('empresas'));
    }

    public function entregas()
    {
        $entregas = Entrega::with([
            'empresa.usuario',
            'entregador.usuario',
            'enderecoOrigem',
            'enderecoDestino',
        ])
            ->latest()
            ->get();

        return view('admin.entrega', compact('entregas'));
    }

    public function destroyEntregador(Entregador $entregador)
    {
        $usuario = $entregador->usuario;
        $entregador->delete();

        if ($usuario) {
            $usuario->delete();
        }

        return back()->with(
            'success',
            'Entregador removido com sucesso.'
        );
    }

    public function destroyEmpresa(Empresa $empresa)
    {
        $usuario = $empresa->usuario;

        $empresa->delete();

        if ($usuario) {
            $usuario->delete();
        }

        return back()->with(
            'success',
            'Empresa removida com sucesso.'
        );
    }

    public function destroyEntrega(Entrega $entrega)
    {
        $entrega->delete();

        return back()->with(
            'success',
            'Entrega removida com sucesso.'
        );
    }

    // Falta ainda o criar e editar de cada um..
}