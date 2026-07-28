<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Entregador;
use App\Models\Entrega;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalEmpresas    = Empresa::count();
        $totalEntregadores = Entregador::count();
        $totalEntregas    = Entrega::count();
        $ultimasEntregas  = Entrega::with(['empresa', 'entregador'])->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalEmpresas', 
            'totalEntregadores', 
            'totalEntregas', 
            'ultimasEntregas'
        ));
    }
}