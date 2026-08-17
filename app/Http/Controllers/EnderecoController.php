<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Empresa;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnderecoController extends Controller
{
   
    public function index()
    {
        $empresa = Empresa::where('usuario_id', Auth::id())
            ->with('endereco')
            ->firstOrFail();

        $enderecoEmpresa = $empresa->endereco;

        $idsOrigem = Entrega::where('empresa_id', $empresa->id)
            ->whereNotNull('endereco_origem_id')
            ->pluck('endereco_origem_id');

        $idsDestino = Entrega::where('empresa_id', $empresa->id)
            ->whereNotNull('endereco_destino_id')
            ->pluck('endereco_destino_id');

        $idsEnderecos = $idsOrigem
            ->merge($idsDestino)
            ->unique();

        if ($enderecoEmpresa) {
            $idsEnderecos = $idsEnderecos
                ->reject(function ($id) use ($enderecoEmpresa) {
                    return $id == $enderecoEmpresa->id;
                });
        }

        $enderecos = Endereco::whereIn('id', $idsEnderecos)
            ->orderBy('id', 'desc')
            ->get();


        return view('empresa.enderecos', compact(
            'empresa',
            'enderecoEmpresa',
            'enderecos'
        ));
    }


    public function create()
    {
        return view('empresa.endereco-form', [
            'endereco' => null
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([

            'logradouro' => [
                'required',
                'string',
                'max:255'
            ],

            'numero' => [
                'required',
                'string',
                'max:20'
            ],

            'bairro' => [
                'required',
                'string',
                'max:255'
            ],

            'cidade' => [
                'required',
                'string',
                'max:255'
            ],

            'estado' => [
                'required',
                'string',
                'size:2'
            ],

            'cep' => [
                'required',
                'string',
                'max:9'
            ],

            'complemento' => [
                'nullable',
                'string',
                'max:255'
            ],

            'latitude' => [
                'nullable',
                'numeric'
            ],

            'longitude' => [
                'nullable',
                'numeric'
            ],

        ]);


        $dados['estado'] = strtoupper($dados['estado']);


        Endereco::create($dados);


        return redirect()
            ->route('empresa.enderecos')
            ->with('success', 'Endereço cadastrado com sucesso.');
    }


    public function edit(Endereco $endereco)
    {
        $this->verificarAcesso($endereco);

        return view('empresa.endereco-form', compact('endereco'));
    }

    public function update(Request $request, Endereco $endereco)
    {
        $this->verificarAcesso($endereco);

        $dados = $request->validate([

            'logradouro' => [
                'required',
                'string',
                'max:255'
            ],

            'numero' => [
                'required',
                'string',
                'max:20'
            ],

            'bairro' => [
                'required',
                'string',
                'max:255'
            ],

            'cidade' => [
                'required',
                'string',
                'max:255'
            ],

            'estado' => [
                'required',
                'string',
                'size:2'
            ],

            'cep' => [
                'required',
                'string',
                'max:9'
            ],

            'complemento' => [
                'nullable',
                'string',
                'max:255'
            ],

            'latitude' => [
                'nullable',
                'numeric'
            ],

            'longitude' => [
                'nullable',
                'numeric'
            ],

        ]);


        $dados['estado'] = strtoupper($dados['estado']);


        $endereco->update($dados);


        return redirect()
            ->route('empresa.enderecos')
            ->with('success', 'Endereço atualizado com sucesso.');
    }

    public function destroy(Endereco $endereco)
    {
        $this->verificarAcesso($endereco);

        $empresa = Empresa::where('usuario_id', Auth::id())
            ->firstOrFail();


        if ($empresa->endereco_id == $endereco->id) {

            return redirect()
                ->route('empresa.enderecos')
                ->with('error', 'O endereço principal da empresa não pode ser excluído.');
        }


        $endereco->delete();


        return redirect()
            ->route('empresa.enderecos')
            ->with('success', 'Endereço excluído com sucesso.');
    }


    private function verificarAcesso(Endereco $endereco)
    {
        $empresa = Empresa::where('usuario_id', Auth::id())
            ->firstOrFail();

        if ($empresa->endereco_id == $endereco->id) {
            return;
        }

        $pertence = Entrega::where('empresa_id', $empresa->id)
            ->where(function ($query) use ($endereco) {

                $query
                    ->where('endereco_origem_id', $endereco->id)
                    ->orWhere('endereco_destino_id', $endereco->id);

            })
            ->exists();


        if (!$pertence) {
            abort(403);
        }
    }
}