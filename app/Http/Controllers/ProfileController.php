<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Visualizar perfil
     */
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Editar perfil
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atualizar perfil
     */
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

    /**
     * Excluir conta
     */
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