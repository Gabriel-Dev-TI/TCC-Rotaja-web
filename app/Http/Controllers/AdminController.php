<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Entregador;
use App\Models\Entrega;

class AdminController extends Controller
{
    public function index()
{
    $totalEntregas = Entrega::count();
    $totalEmpresas = Empresa::count();
    $totalEntregadores = Entregador::count();

    $ultimasEntregas = Entrega::with(['empresa', 'entregador'])
        ->latest()
        ->take(8)
        ->get();

    return view('admin.dashboard', [
        'totalEntregas' => $totalEntregas,
        'totalEmpresas' => $totalEmpresas,
        'totalEntregadores' => $totalEntregadores,
        'ultimasEntregas' => $ultimasEntregas,
    ]);
}
}