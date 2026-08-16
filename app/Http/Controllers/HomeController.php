<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Empresa;
use App\Models\Entregador;

class HomeController extends Controller
{
    public function index()
    {
        $pendentes = Entrega::where('status', 'pendente')->count();

        $emTransito = Entrega::where('status', 'em_transito')->count();

        $concluidas = Entrega::where('status', 'concluido')->count();

        $totalEmpresas = Empresa::count();

        $totalEntregadores = Entregador::count();

        return view('welcome', compact(
            'pendentes',
            'emTransito',
            'concluidas',
            'totalEmpresas',
            'totalEntregadores'
        ));
    }
}