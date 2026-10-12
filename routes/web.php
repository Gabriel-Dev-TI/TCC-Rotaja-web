<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EntregadorController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

// Redireciona para o dashboard respectivo caso o usuário esteja autenticado ao acessar o site
Route::get('/', function () {
    if (auth()->check()) {

        return match (auth()->user()->cargo) {
            'admin' => redirect()->route('admin.dashboard'),
            'empresa' => redirect()->route('empresa.dashboard'),
            'entregador' => redirect()->route('entregador.dashboard'),
            default => redirect()->route('login'),
        };

    }
    return view('landingpage');
})->name('home');


Route::middleware(['auth'])->group(function () {

    // Rotas para todos os usuários
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::get('/perfil/edit', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('perfil.destroy');
    Route::get('/historico', [EntregaController::class, 'index'])->name('historico');

    // Rotas Admin
Route::middleware(['cargo:admin'])->group(function () {

    // Dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Entregadores
    Route::get('/admin/entregadores', [AdminController::class, 'entregadores'])->name('admin.entregadores.index');
    Route::delete('/admin/entregadores/{entregador}', [AdminController::class, 'destroyEntregador'])->name('admin.entregadores.destroy');

    //Empresas
    Route::get('/admin/empresas', [AdminController::class, 'empresas'])->name('admin.empresas.index');
    Route::delete('/admin/empresas/{empresa}', [AdminController::class, 'destroyEmpresa'])->name('admin.empresas.destroy');

    //Entregas
    Route::get('/admin/entregas', [AdminController::class, 'entregas'])->name('admin.entregas.index');
    Route::delete('/admin/entregas/{entrega}', [AdminController::class, 'destroyEntrega'])->name('admin.entregas.destroy');

});

    // Rotas Empresa
    Route::middleware(['cargo:empresa'])->group(function () {
        Route::get('/empresa', [EmpresaController::class, 'index'])->name('empresa.dashboard');
        Route::get('/entregas/cadastrar', [EntregaController::class,'create'])->name('entregas.create');
        Route::post('/entregas', [EntregaController::class,'store'])->name('empresa.entregas.store');
        Route::delete('/entregas/{entrega}', [EntregaController::class,'destroy'])->name('empresa.entregas.destroy');
        Route::resource('enderecos', EnderecoController::class);
    });

    // Rotas Entregador
    Route::middleware(['cargo:entregador'])->group(function () {
        Route::get('/entregador', [EntregadorController::class, 'index'])->name('entregador.dashboard');
        Route::get('/rota', [EntregadorController::class,'rota'])->name('rota');
        Route::post('/entregador/entrega/{id}/aceitar',[EntregadorController::class, 'aceitarEntrega'])->name('entregador.entrega.aceitar');
        Route::patch('/entrega/{entrega}/finalizar', [EntregadorController::class, 'finalizar'])->name('entrega.finalizar');
        Route::post('/entrega/{entrega}/cancelar', [EntregadorController::class, 'cancelar'])->name('entrega.cancelar');
        Route::post('/entregas/{entrega}/observacao',[EntregadorController::class, 'observacao'])->name('entrega.observacao');
        
    });
});

require __DIR__.'/auth.php';