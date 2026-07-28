<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\EntregadorController;
use App\Http\Controllers\Api\EntregaController;

//Login
Route::post('/login', [UsuarioController::class, 'login']);

//Cadastro
Route::post('/entregadores',[EntregadorController::class,'store']);
Route::post('/empresas',[EmpresaController::class,'store']);
Route::post('/enderecos',[EnderecoController::class,'store']);

//Rotas que exigem Token Bearer do Sanctum
Route::middleware('auth:sanctum')->group(function () {

    //Perfil
    Route::get('/logout', [UsuarioController::class, 'logout']);
    Route::get('/meu-perfil', [UsuarioController::class, 'meuPerfil']);
    Route::put('/atualizar-senha', [UsuarioController::class, 'atualizarSenha']);

    //Listagens
    Route::get('/historico', [UsuarioController::class, 'historico']);
    Route::apiResource('entregas', EntregaController::class);
});
