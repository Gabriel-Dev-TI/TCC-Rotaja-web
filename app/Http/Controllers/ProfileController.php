<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function show(Request $request): View
    {
        $usuario = $request->user();

        $cargo = $usuario->cargo;

        $nomeCargo = match ($cargo) {
            'admin' => 'Administrador',
            'empresa' => 'Empresa',
            'entregador' => 'Entregador',
        };

        $inicial = strtoupper(substr($usuario->nome, 0, 1));

        $empresa = $cargo === 'empresa'
            ? $usuario->empresa
            : null;

        $entregador = $cargo === 'entregador'
            ? $usuario->entregador
            : null;

        $endereco = $empresa?->enderecos
            ? $empresa->enderecos->where('tipo', 'proprio')->first()
            : null;

        $enderecoFormatado = $endereco
            ? trim(
                ($endereco->logradouro ?? '') .
                (isset($endereco->numero) ? ', ' . $endereco->numero : '') .
                (!empty($endereco->bairro) ? ' - ' . $endereco->bairro : '') .
                (!empty($endereco->cidade) ? ' - ' . $endereco->cidade : '') .
                (!empty($endereco->estado) ? '/' . $endereco->estado : '')
            )
            : null;

        return view('profile.show', [
            'usuario' => $usuario,
            'cargo' => $cargo,
            'nomeCargo' => $nomeCargo,
            'inicial' => $inicial,
            'empresa' => $empresa,
            'entregador' => $entregador,
            'endereco' => $endereco,
            'enderecoFormatado' => $enderecoFormatado,
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'telefone' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        $user = $request->user();

        $user->nome = $request->input('nome');
        $user->email = $request->input('email');
        $user->telefone = $request->input('telefone');

        $user->save();

        return redirect()
            ->route('perfil.show')
            ->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => [
                'required',
                'current_password',
            ],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}