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
        //Alguns campos precisam ser nullable
        //Caso esteja sendo cadastrado uma empresa os campos do entregador precisam ser nulos
        //E se for entregador os campos empresas precisam ser nulos

        $request->validate([
            'nome' => ['required','string','max:255',],
            'email' => ['required','string','email','max:255','unique:' . User::class,],
            'senha' => ['required','confirmed',Rules\Password::defaults(),],
            'telefone' => ['required','string','max:20',],
            'tipo' => ['required','in:empresa,entregador',],

            // EMPRESA
            'cnpj' => ['required_if:tipo,empresa','nullable','string','max:18','unique:' . Empresa::class,],
            'logradouro' => ['required_if:tipo,empresa','nullable','string','max:255',],
            'numero' => ['required_if:tipo,empresa','nullable','string','max:20',],
            'bairro' => ['required_if:tipo,empresa','nullable','string','max:255',],
            'cidade' => ['required_if:tipo,empresa','nullable','string','max:255',],
            'estado' => ['required_if:tipo,empresa','nullable','string',],
            'cep' => ['required_if:tipo,empresa','nullable','string','max:9',],
            'complemento' => ['nullable','string','max:255',],

            // ENTREGADOR
            'cpf' => ['required_if:tipo,entregador','nullable','string','max:14','unique:' . Entregador::class,],
            'tipo_veiculo' => ['required_if:tipo,entregador','nullable','string','max:50',],
        ]);

        //Se for empresa verifica antes de cadastrar se o endereço possui cordenadas
        $coordenadas = null;
        if ($request->tipo === 'empresa') {

            // Formata o endereço para o ArcGIS
            $enderecoCompleto = sprintf(
                '%s %s, %s, %s, %s',
                $request->logradouro,
                $request->numero,
                $request->bairro,
                $request->cidade,
                $request->estado
            );

            // Consulta o ArcGIS
            $resposta = Http::timeout(10)
                ->get(
                    'https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates',
                    [
                        'SingleLine' => $enderecoCompleto,
                        'maxLocations' => 5,
                        'outFields' => '*',
                        'f' => 'json',
                    ]
                );

            if ($resposta->successful()) {

                $dados = $resposta->json();

                $candidatos = $dados['candidates'] ?? [];

                if (!empty($candidatos)) {

                    foreach ($candidatos as $candidato) {

                        $score = $candidato['attributes']['Score'] ?? 0;

                        $tipoEndereco = $candidato['attributes']['Addr_type'] ?? '';

                        $numeroEncontrado = trim((string) ($candidato['attributes']['AddNum'] ?? ''));
                        $numeroInformado = trim((string) $request->numero);

                        /*
                        * Aceitamos somente correspondências
                        * com score alto e que realmente tenham
                        * o número informado.
                        *
                        * No campo Addr_type verificamos se é
                        *   PointAddress: Localizou exatamente o endereço
                        *   StreetAddressExt: Localizou a rua
                        */
                        if (
                            $score >= 90 &&
                            $numeroEncontrado === $numeroInformado &&
                            in_array($tipoEndereco, [
                                'PointAddress',
                                'StreetAddressExt',
                            ])
                        ) {

                            $coordenadas = [
                                'latitude' =>
                                    $candidato['location']['y'],

                                'longitude' =>
                                    $candidato['location']['x'],
                            ];

                            break;
                        }
                    }
                }
            }

            //Se não achar endereço,retorna o erro
            if ($coordenadas === null) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'endereco' =>
                            'Não foi possível localizar exatamente o endereço informado. Verifique o endereço.',
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