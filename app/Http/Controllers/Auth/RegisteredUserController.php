<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Endereco;
use App\Models\Entregador;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        return view('auth.register', [
            'tipo' => $request->query('tipo'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Usuário
            |--------------------------------------------------------------------------
            */

            'nome' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],

            'senha' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],

            'telefone' => [
                'required',
                'string',
                'max:20',
            ],

            'tipo' => [
                'required',
                'in:empresa,entregador',
            ],


            /*
            |--------------------------------------------------------------------------
            | Empresa
            |--------------------------------------------------------------------------
            */

            'cnpj' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:18',
            ],

            /*
            |--------------------------------------------------------------------------
            | Endereço da empresa
            |--------------------------------------------------------------------------
            */

            'logradouro' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:255',
            ],

            'numero' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:20',
            ],

            'bairro' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:255',
            ],

            'cidade' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:255',
            ],

            'estado' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:2',
            ],

            'cep' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:9',
            ],

            'complemento' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Entregador
            |--------------------------------------------------------------------------
            */

            'cpf' => [
                'required_if:tipo,entregador',
                'nullable',
                'string',
                'max:14',
            ],

            'placa' => [
                'required_if:tipo,entregador',
                'nullable',
                'string',
                'max:10',
            ],

            'tipo_veiculo' => [
                'required_if:tipo,entregador',
                'nullable',
                'string',
                'max:50',
            ],
        ]);


        $usuario = DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | Cria o usuário
            |--------------------------------------------------------------------------
            */

            $usuario = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => $request->senha,
                'telefone' => $request->telefone,
                'cargo' => $request->tipo,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Empresa
            |--------------------------------------------------------------------------
            */

            if ($request->tipo === 'empresa') {

                /*
                * Primeiro cria o endereço
                */
                $endereco = Endereco::create([
                    'logradouro' => $request->logradouro,
                    'numero' => $request->numero,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'estado' => $request->estado,
                    'cep' => $request->cep,
                    'complemento' => $request->complemento,
                    'latitude' => null,
                    'longitude' => null,
                ]);


                /*
                * Depois cria a empresa ligada ao usuário
                * e ao endereço
                */
                Empresa::create([
                    'usuario_id' => $usuario->id,
                    'cnpj' => $request->cnpj,
                    'endereco_id' => $endereco->id,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Entregador
            |--------------------------------------------------------------------------
            */

            if ($request->tipo === 'entregador') {

                Entregador::create([
                    'usuario_id' => $usuario->id,
                    'cpf' => $request->cpf,
                    'placa' => $request->placa,
                    'tipo_veiculo' => $request->tipo_veiculo,
                ]);
            }


            return $usuario;
        });


        /*
        |--------------------------------------------------------------------------
        | Evento de registro
        |--------------------------------------------------------------------------
        */

        event(new Registered($usuario));


        /*
        |--------------------------------------------------------------------------
        | Login automático
        |--------------------------------------------------------------------------
        */

        Auth::login($usuario);


        /*
        |--------------------------------------------------------------------------
        | Dashboard de acordo com o tipo
        |--------------------------------------------------------------------------
        */

        return redirect()->route(
            $usuario->cargo . '.dashboard'
        );
    }
}