<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Empresa;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EnderecoController extends Controller
{
   
    public function index()
    {
        $empresa = Empresa::where('usuario_id', Auth::id())
            ->with('enderecos')
            ->firstOrFail();

        $enderecoEmpresa = Endereco::where('empresa_id', $empresa->id)
            ->where('tipo', 'proprio')
            ->first();

        $enderecos = Endereco::where('empresa_id', $empresa->id)
            ->where('tipo', 'entrega')
            ->latest()
            ->get();


        return view('empresa.enderecos', compact(
            'empresa',
            'enderecoEmpresa',
            'enderecos'
        ));
    }


    public function create()
    {
        // Tem que passar o endereco como null
        // Se passar algum endereco vira pagina de edição
        return view('empresa.endereco-form', [
            'endereco' => null
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'logradouro' => ['required','string','max:255'],
            'numero' => ['required','string','max:20'],
            'bairro' => ['required','string','max:255'],
            'cidade' => ['required','string','max:255'],
            'estado' => ['required','string'],
            'cep' => ['required','string','max:9'],
            'complemento' => ['nullable','string','max:255'],
        ]);

         // Para saber se o endereço é valido verifica as cordenadas
        $coordenadas = null;

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

                $dado = $resposta->json();

                $candidatos = $dado['candidates'] ?? [];

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
                                'latitude' => $candidato['location']['y'],
                                'longitude' => $candidato['location']['x'],
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
        
        $dados['latitude'] = $coordenadas['latitude'];
        $dados['longitude'] = $coordenadas['longitude'];

        $empresa = Empresa::where('usuario_id', Auth::id()) ->firstOrFail();
        $dados['empresa_id'] = $empresa->id;
        $dados['tipo'] = 'entrega';

        Endereco::create($dados);

        return redirect()
            ->route('enderecos.index')
            ->with('success', 'Endereço cadastrado com sucesso.');
    }


    public function edit(Endereco $endereco)
    {
        //Verifica se o endereço esta em uma entrega
        $empresa = Empresa::where('usuario_id', Auth::id())->firstOrFail();

        $pertence = Entrega::where('empresa_id', $empresa->id)
            ->where(function ($query) use ($endereco) {

                $query
                    ->where('endereco_origem_id', $endereco->id)
                    ->orWhere('endereco_destino_id', $endereco->id);

            })
            ->exists();


        if ($pertence) {

            return redirect()
                ->route('enderecos.index')
                ->with('error', 'O endereço está sendo usado em uma entrega.');
        }

        return view('empresa.endereco-form', compact('endereco'));
    }

    public function update(Request $request, Endereco $endereco)
    {
        //Verifica se o endereço esta em uma entrega
        $empresa = Empresa::where('usuario_id', Auth::id())->firstOrFail();

        $pertence = Entrega::where('empresa_id', $empresa->id)
            ->where(function ($query) use ($endereco) {

                $query
                    ->where('endereco_origem_id', $endereco->id)
                    ->orWhere('endereco_destino_id', $endereco->id);

            })
            ->exists();


        if ($pertence) {

            return redirect()
                ->route('enderecos.index')
                ->with('error', 'O endereço está sendo usado em uma entrega.');
        }

        $dados = $request->validate([
            'logradouro' => ['required','string','max:255'],
            'numero' => ['required','string','max:20'],
            'bairro' => ['required','string','max:255'],
            'cidade' => ['required','string','max:255'],
            'estado' => ['required','string'],
            'cep' => ['required','string','max:9'],
            'complemento' => ['nullable','string','max:255'],
        ]);

         // Para saber se o endereço é valido verifica as cordenadas
        $coordenadas = null;

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

                $dado = $resposta->json();

                $candidatos = $dado['candidates'] ?? [];

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
                                'latitude' => $candidato['location']['y'],
                                'longitude' => $candidato['location']['x'],
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
        
        $dados['latitude'] = $coordenadas['latitude'];
        $dados['longitude'] = $coordenadas['longitude'];

        $empresa = Empresa::where('usuario_id', Auth::id()) ->firstOrFail();
        $dados['empresa_id'] = $empresa->id;
        $dados['tipo'] = 'entrega';

        $endereco->update($dados);

        return redirect()
            ->route('enderecos.index')
            ->with('success', 'Endereço atualizado com sucesso.');
    }

    public function destroy(Endereco $endereco)
    {
        //Verifica se o endereço esta em uma entrega
        $empresa = Empresa::where('usuario_id', Auth::id())->firstOrFail();

        if ($empresa->endereco_id == $endereco->id) {

            return redirect()
                ->route('enderecos.index')
                ->with('error', 'O endereço principal da empresa não pode ser excluído.');
        }

        $pertence = Entrega::where('empresa_id', $empresa->id)
            ->where(function ($query) use ($endereco) {

                $query
                    ->where('endereco_origem_id', $endereco->id)
                    ->orWhere('endereco_destino_id', $endereco->id);

            })
            ->exists();


        //Se o endereço foi usado em uma entrega usa o softdelete
        if ($pertence) {
            $endereco->delete();
        }
        else{
            $endereco->forceDelete();
        }

        return redirect()
            ->route('enderecos.index')
            ->with('success', 'Endereço excluído com sucesso.');
    }

}