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
    Route::get('/historico', [EntregaController::class, 'historico'])->name('historico');

    // Rotas Admin
Route::middleware(['cargo:admin'])->group(function () {

    // Dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Entregadores
    Route::get('/admin/entregadores', [AdminController::class, 'entregadores'])->name('admin.entregadores.index');
    Route::get('/admin/entregadores/criar', [AdminController::class, 'createEntregador'])->name('admin.entregadores.create');
    Route::post('/admin/entregadores', [AdminController::class, 'storeEntregador'])->name('admin.entregadores.store');
    Route::get('/admin/entregadores/{entregador}/editar', [AdminController::class, 'editEntregador'])->name('admin.entregadores.edit');
    Route::patch('/admin/entregadores/{entregador}', [AdminController::class, 'updateEntregador'])->name('admin.entregadores.update');
    Route::delete('/admin/entregadores/{entregador}', [AdminController::class, 'destroyEntregador'])->name('admin.entregadores.destroy');

    //Empresas
    Route::get('/admin/empresas', [AdminController::class, 'empresas'])->name('admin.empresas.index');
    Route::get('/admin/empresas/criar', [AdminController::class, 'createEmpresa'])->name('admin.empresas.create');
    Route::post('/admin/empresas', [AdminController::class, 'storeEmpresa'])->name('admin.empresas.store');
    Route::get('/admin/empresas/{empresa}/editar', [AdminController::class, 'editEmpresa'])->name('admin.empresas.edit');
    Route::patch('/admin/empresas/{empresa}', [AdminController::class, 'updateEmpresa'])->name('admin.empresas.update');
    Route::delete('/admin/empresas/{empresa}', [AdminController::class, 'destroyEmpresa'])->name('admin.empresas.destroy');

    //Entregas
    Route::get('/admin/entregas', [AdminController::class, 'entregas'])->name('admin.entregas.index');
    Route::get('/admin/entregas/criar', [AdminController::class, 'createEntrega'])->name('admin.entregas.create');
    Route::post('/admin/entregas', [AdminController::class, 'storeEntrega'])->name('admin.entregas.store');
    Route::get('/admin/entregas/{entrega}/editar', [AdminController::class, 'editEntrega'])->name('admin.entregas.edit');
    Route::patch('/admin/entregas/{entrega}', [AdminController::class, 'updateEntrega'])->name('admin.entregas.update');
    Route::delete('/admin/entregas/{entrega}', [AdminController::class, 'destroyEntrega'])->name('admin.entregas.destroy');

});

    // Rotas Empresa
    Route::middleware(['cargo:empresa'])->group(function () {
        Route::get('/empresa', [EmpresaController::class, 'empresa'])->name('empresa.dashboard');
        Route::get('/entregas/cadastrar', [EntregaController::class,'create'])->name('entregas.create');
        Route::post('/entregas', [EntregaController::class,'store'])->name('empresa.entregas.store');
        Route::get('/enderecos', [EnderecoController::class, 'index'])->name('enderecos.index');
        Route::get('/enderecos/cadastrar', [EnderecoController::class, 'create'])->name('empresa.enderecos.create');
        Route::post('/enderecos', [EnderecoController::class, 'store'])->name('enderecos.store');
        Route::get('/enderecos/{endereco}/editar', [EnderecoController::class, 'edit'])->name('empresa.enderecos.edit');
        Route::patch('/enderecos/{endereco}', [EnderecoController::class, 'update'])->name('empresa.enderecos.update');
        Route::delete('/enderecos/{endereco}', [EnderecoController::class, 'destroy'])->name('empresa.enderecos.destroy');
    });

    // Rotas Entregador
    Route::middleware(['cargo:entregador'])->group(function () {
        Route::get('/entregador', [EntregadorController::class, 'index'])->name('entregador.dashboard');
        Route::get('/rota', [EntregaController::class,'rota'])->name('rota');
        Route::post('/entregador/entrega/{id}/aceitar',[EntregadorController::class, 'aceitarEntrega'])->name('entregador.entrega.aceitar');
        Route::patch('/entrega/{entrega}/finalizar', [EntregaController::class, 'finalizar'])->name('entrega.finalizar');
        Route::post('/entrega/{entrega}/ocorrencia', [EntregaController::class, 'ocorrencia'])->name('entrega.ocorrencia');
        Route::post('/entregas/{entrega}/observacao',[EntregaController::class, 'observacao'])->name('entregas.observacao');
        Route::patch('/entregas/{entrega}/finalizar',[EntregaController::class, 'finalizar'])->name('entregas.finalizar');
        
    });
});

require __DIR__.'/auth.php';