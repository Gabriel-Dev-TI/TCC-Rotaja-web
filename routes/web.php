<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EntregadorController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\AdminController;


/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (auth()->check()) {

        return match (auth()->user()->cargo) {

            'admin' => redirect()->route('admin.dashboard'),

            'empresa' => redirect()->route('empresa.dashboard'),

            'entregador' => redirect()->route('entregador.dashboard'),

            default => redirect()->route('login'),

        };

    }

    return view('welcome');

})->name('home');


/*
|--------------------------------------------------------------------------
| Cadastros públicos
|--------------------------------------------------------------------------
|
| Essas rotas são usadas pela landing page.
|
*/

Route::get(
    '/empresas/cadastrar',
    [EmpresaController::class, 'create']
)->name('empresas.create');

Route::post(
    '/empresas/cadastrar',
    [EmpresaController::class, 'store']
)->name('empresas.store');


Route::get(
    '/entregadores/cadastrar',
    [EntregadorController::class, 'create']
)->name('entregadores.create');

Route::post(
    '/entregadores/cadastrar',
    [EntregadorController::class, 'store']
)->name('entregadores.store');


/*
|--------------------------------------------------------------------------
| Rotas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Rotas comuns
    |--------------------------------------------------------------------------
    |
    | Todos os usuários autenticados podem acessar.
    |
    */

    Route::get('/perfil', function () {
        return view('usuario.perfil');
    })->name('perfil');


    Route::get('/configuracoes', [
        EmpresaController::class,
        'configuracoes'
    ])->name('perfil.configuracoes');


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware(['cargo:admin'])->group(function () {

        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');


        /*
        |--------------------------------------------------------------------------
        | Entregadores
        |--------------------------------------------------------------------------
        */

        Route::get('/entregadores', [
            EntregadorController::class,
            'index'
        ])->name('entregadores.index');


        /*
        |--------------------------------------------------------------------------
        | Empresas
        |--------------------------------------------------------------------------
        */

        Route::get('/empresas', [
            EmpresaController::class,
            'index'
        ])->name('empresas.index');


        /*
        |--------------------------------------------------------------------------
        | Entregas
        |--------------------------------------------------------------------------
        */

        Route::get('/entregas', [
            EntregaController::class,
            'index'
        ])->name('entregas.index');

    });


    /*
    |--------------------------------------------------------------------------
    | EMPRESA
    |--------------------------------------------------------------------------
    */

    Route::middleware(['cargo:empresa'])->group(function () {

        Route::get('/empresa', function () {
            return view('empresa.dashboard');
        })->name('empresa.dashboard');


        /*
        |--------------------------------------------------------------------------
        | Cadastrar entrega
        |--------------------------------------------------------------------------
        */

        Route::get('/entregas/cadastrar', [
            EntregaController::class,
            'create'
        ])->name('entregas.create');


        Route::post('/entregas', [
            EntregaController::class,
            'store'
        ])->name('entregas.store');


        /*
        |--------------------------------------------------------------------------
        | Endereços
        |--------------------------------------------------------------------------
        */

        Route::get('/enderecos', [
            EmpresaController::class,
            'enderecos'
        ])->name('enderecos.index');


        Route::get('/enderecos/cadastrar', [
            EmpresaController::class,
            'createEndereco'
        ])->name('enderecos.create');


        Route::post('/enderecos', [
            EmpresaController::class,
            'storeEndereco'
        ])->name('enderecos.store');

    });


    /*
    |--------------------------------------------------------------------------
    | ENTREGADOR
    |--------------------------------------------------------------------------
    */

    Route::middleware(['cargo:entregador'])->group(function () {

        Route::get('/entregador', function () {
            return view('entregador.dashboard');
        })->name('entregador.dashboard');


        /*
        |--------------------------------------------------------------------------
        | Rota
        |--------------------------------------------------------------------------
        */

        Route::get('/rota', [
            EntregaController::class,
            'rota'
        ])->name('rota');


        /*
        |--------------------------------------------------------------------------
        | Aceitar entrega
        |--------------------------------------------------------------------------
        */

        Route::post('/entrega/{id}/aceitar', [
            EntregaController::class,
            'aceitar'
        ])->name('entregas.aceitar');


        /*
        |--------------------------------------------------------------------------
        | Finalizar entrega
        |--------------------------------------------------------------------------
        */

        Route::post('/entrega/{id}/finalizar', [
            EntregaController::class,
            'finalizar'
        ])->name('entregas.finalizar');

    });

});


/*
|--------------------------------------------------------------------------
| Autenticação Breeze
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';