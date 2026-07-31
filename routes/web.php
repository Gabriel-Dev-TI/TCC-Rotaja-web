<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EntregadorController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Landing Page e Cadastros)
|--------------------------------------------------------------------------
*/

// Landing Page Principal (Estilo do seu HTML)
Route::get('/', function () {
    return view('index');
})->name('home');

// Cadastro Público de Empresas
Route::get('/empresas/cadastrar', [EmpresaController::class, 'create'])->name('empresas.create');
Route::post('/empresas/cadastrar', [EmpresaController::class, 'store'])->name('empresas.store');

// Cadastro Público de Entregadores
Route::get('/entregadores/cadastrar', [EntregadorController::class, 'create'])->name('entregadores.create');
Route::post('/entregadores/cadastrar', [EntregadorController::class, 'store'])->name('entregadores.store');

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas (Apenas Usuários Logados)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Painel e Ações da Empresa
    Route::get('/empresa/painel', [EmpresaController::class, 'dashboard'])->name('empresa.dashboard');
    Route::post('/entrega/nova', [EntregaController::class, 'store'])->name('entregas.store');

    // Painel e Ações do Entregador
    Route::get('/entregador/painel', [EntregadorController::class, 'dashboard'])->name('entregador.dashboard');
    Route::post('/entrega/{id}/aceitar', [EntregaController::class, 'aceitar'])->name('entregas.aceitar');
    Route::post('/entrega/{id}/finalizar', [EntregaController::class, 'finalizar'])->name('entregas.finalizar');

    // Histórico e Configurações de Perfil
    Route::get('/historico', [EntregaController::class, 'historico'])->name('entregas.historico');
    Route::get('/configuracoes', [EmpresaController::class, 'configuracoes'])->name('perfil.configuracoes');

    // Painel Administrativo Geral
    Route::get('/admin/painel', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação do Breeze (Login, Register, Logout)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';