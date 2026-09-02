@extends('layouts.app')

@section('title', $endereco ? 'Editar endereço' : 'Novo endereço')

@section('content')

<div class="mb-3">

    <h1 class="h3 mb-1">

        {{ $endereco ? 'Editar endereço' : 'Novo endereço' }}

    </h1>

    <p class="text-muted mb-0">

        {{ $endereco
            ? 'Atualize os dados do endereço.'
            : 'Cadastre um novo endereço para suas entregas.'
        }}

    </p>

</div>


@if($errors->any())

    <div class="alert alert-danger">

        <strong>
            Verifique:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $erro)

                <li class="text-danger m-1">
                    {{ $erro }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


<div class="card">

    <div class="card-body">

        <form
            method="POST"
            action="{{
                $endereco
                    ? route('empresa.enderecos.update', $endereco)
                    : route('empresa.enderecos.store')
            }}"
        >

            @csrf

            @if($endereco)

                @method('PATCH')

            @endif


            <div class="row">

                <div class="col-md-8 mb-3">

                    <label class="form-label">
                        Logradouro
                    </label>

                    <input
                        type="text"
                        name="logradouro"
                        id="logradouro"
                        class="form-control"
                        value="{{ old('logradouro', $endereco->logradouro ?? '') }}"
                        required
                    >

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Número
                    </label>

                    <input
                        type="number"
                        name="numero"
                        class="form-control"
                        value="{{ old('numero', $endereco->numero ?? '') }}"
                        required
                    >

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Bairro
                    </label>

                    <input
                        type="text"
                        name="bairro"
                        id="bairro"
                        class="form-control"
                        value="{{ old('bairro', $endereco->bairro ?? '') }}"
                        required
                    >

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Cidade
                    </label>

                    <input
                        type="text"
                        name="cidade"
                        id="cidade"
                        class="form-control"
                        value="{{ old('cidade', $endereco->cidade ?? '') }}"
                        required
                    >

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Estado
                    </label>

                    <input
                        type="text"
                        name="estado"
                        id="estado"
                        class="form-control"
                        placeholder="Estado"
                        value="{{ old('estado', $endereco->estado ?? '') }}"
                        required
                    >

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        CEP
                    </label>

                    <input
                        type="text"
                        name="cep"
                        id="cep"
                        class="form-control"
                        maxlength="9"
                        placeholder="00000-000"
                        value="{{ old('cep', $endereco->cep ?? '') }}"
                        required
                    >

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Complemento
                    </label>

                    <input
                        type="text"
                        name="complemento"
                        class="form-control"
                        value="{{ old('complemento', $endereco->complemento ?? '') }}"
                    >

                </div>

            </div>


            <div class="d-flex justify-content-end gap-2 mt-3">

                <a
                    href="{{ route('enderecos.index') }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i
                        data-feather="save"
                        class="me-1"
                    ></i>

                    {{ $endereco ? 'Salvar alterações' : 'Cadastrar endereço' }}

                </button>

            </div>

        </form>

    </div>

</div>

<script src="https://unpkg.com/imask"></script>   

<script>

//Busca as informações do cep ao terminar de digitar o cep
const cep = document.getElementById('cep');

cep.addEventListener('input', async function () {
    let valor = this.value.replace(/\D/g, '');

    IMask(document.getElementById('cep'), { mask: '00000-000' });

    if (valor.length !== 8) return;

    try {
        const resposta = await fetch(
            `https://viacep.com.br/ws/${valor}/json/`
        );

        if (!resposta.ok) {
            throw new Error('Erro ao consultar CEP');
        }

        const dados = await resposta.json();

        if (dados.erro) {
            return;
        }

        document.getElementById('logradouro').value = dados.logradouro || '';
        document.getElementById('bairro').value = dados.bairro || '';
        document.getElementById('cidade').value = dados.localidade || '';
        document.getElementById('estado').value = dados.estado || '';

    } catch (erro) {
        console.error('Erro ao consultar CEP:', erro);
    }
});
</script>


@endsection