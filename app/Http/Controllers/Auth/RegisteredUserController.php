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
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
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
            'nome' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
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

            // EMPRESA
            'cnpj' => [
                'required_if:tipo,empresa',
                'nullable',
                'string',
                'max:18',
            ],

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

            // ENTREGADOR
            'cpf' => [
                'required_if:tipo,entregador',
                'nullable',
                'string',
                'max:14',
            ],

            'tipo_veiculo' => [
                'required_if:tipo,entregador',
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        //Primeiro  Verifica se o endereço é valido
        $coordenadas = null;

if ($request->tipo === 'empresa') {

    // 1. Tenta pelo endereço usando Nominatim
    $enderecoCompleto = sprintf(
        '%s,%s,%s,%s-%s',
        $request->logradouro,
        $request->numero,
        $request->bairro,
        $request->cidade,
        $request->estado
    );

    $resposta = Http::timeout(10)
        ->withHeaders([
            'User-Agent' => 'Rotaja/1.0',
            'Accept-Language' => 'pt-BR',
        ])
        ->get('https://nominatim.openstreetmap.org/search', [
            'format' => 'json',
            'q' => $enderecoCompleto,
        ]);

    if ($resposta->successful()) {

        $resultado = $resposta->json();

        if (!empty($resultado)) {
            $coordenadas = [
                'latitude' => $resultado[0]['lat'],
                'longitude' => $resultado[0]['lon'],
            ];
        }
    }

    // 2. Se não encontrou pelo endereço, tenta pelo CEP
    if ($coordenadas === null) {

        $cep = preg_replace('/\D/', '', $request->cep);

        $respostaCep = Http::timeout(10)
            ->get("https://cep.awesomeapi.com.br/json/{$cep}");

        if ($respostaCep->successful()) {

            $dadosCep = $respostaCep->json();

            if (
                isset($dadosCep['lat']) &&
                isset($dadosCep['lng'])
            ) {
                $coordenadas = [
                    'latitude' => $dadosCep['lat'],
                    'longitude' => $dadosCep['lng'],
                ];
            }
        }
    }

    // 3. Só dá erro se os dois métodos falharem
    if ($coordenadas === null) {

        return back()
            ->withInput()
            ->withErrors([
                'endereco' =>
                    'Não foi possível obter as coordenadas do endereço.',
            ]);
    }
}


        $usuario = DB::transaction(function () use ($request, $coordenadas) {

            $usuario = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => $request->senha,
                'telefone' => $request->telefone,
                'cargo' => $request->tipo,
            ]);

            if ($request->tipo === 'empresa') {

                $endereco = Endereco::create([
                    'logradouro' => $request->logradouro,
                    'numero' => $request->numero,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'estado' => $request->estado,
                    'cep' => $request->cep,
                    'complemento' => $request->complemento,
                    'latitude' => $coordenadas['latitude'],
                    'longitude' => $coordenadas['longitude'],
                ]);

                Empresa::create([
                    'usuario_id' => $usuario->id,
                    'cnpj' => $request->cnpj,
                    'endereco_id' => $endereco->id,
                ]);
            }

            if ($request->tipo === 'entregador') {

                Entregador::create([
                    'usuario_id' => $usuario->id,
                    'cpf' => $request->cpf,
                    'tipo_veiculo' => $request->tipo_veiculo,
                ]);
            }

            return $usuario;
        });

        event(new Registered($usuario));

        Auth::login($usuario);

        return redirect()->route(
            $usuario->cargo . '.dashboard'
        );
    }
}