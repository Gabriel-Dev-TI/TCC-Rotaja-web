@extends('layouts.app')

@section('title', 'Nova Entrega')

@section('content')

<div class="container-fluid p-0">

    <div class="mb-3">
        <h1 class="h3 mb-1">Nova <strong>Entrega</strong></h1>
        <p class="text-muted mb-0">
            Preencha os dados do produto e da entrega.
        </p>
    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="card-title mb-0">
                Dados da entrega
            </h5>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Verifique os dados informados:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('entregas.store') }}"
            >

                @csrf

                <div class="row">

                    {{-- NOME DO PRODUTO --}}
                    <div class="col-md-8 mb-3">

                        <label for="nome_produto" class="form-label">
                            Nome do produto
                        </label>

                        <input
                            type="text"
                            id="nome_produto"
                            name="nome_produto"
                            class="form-control"
                            value="{{ old('nome_produto') }}"
                            placeholder="Ex.: Caixa de documentos"
                            required
                        >

                    </div>


                    {{-- PREÇO --}}
                    <div class="col-md-4 mb-3">

                        <label for="preco" class="form-label">
                            Preço da entrega
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                R$
                            </span>

                            <input
                                type="number"
                                id="preco"
                                name="preco"
                                class="form-control"
                                value="{{ old('preco') }}"
                                placeholder="0,00"
                                min="0"
                                step="0.01"
                                required
                            >

                        </div>

                    </div>


                    {{-- ORIGEM --}}
                    <div class="col-md-6 mb-3">

                        <label
                            for="endereco_origem_id"
                            class="form-label"
                        >
                            Endereço de retirada
                        </label>

                        <select
                            id="endereco_origem_id"
                            name="endereco_origem_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Selecione o endereço de retirada
                            </option>

                            @foreach ($enderecos as $endereco)

                                <option
                                    value="{{ $endereco->id }}"
                                    {{ old('endereco_origem_id') == $endereco->id ? 'selected' : '' }}
                                >

                                    {{ $endereco->logradouro }},
                                    {{ $endereco->numero }}
                                    -
                                    {{ $endereco->bairro }},
                                    {{ $endereco->cidade }}/{{ $endereco->estado }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DESTINO --}}
                    <div class="col-md-6 mb-3">

                        <label
                            for="endereco_destino_id"
                            class="form-label"
                        >
                            Endereço de destino
                        </label>

                        <select
                            id="endereco_destino_id"
                            name="endereco_destino_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Selecione o endereço de destino
                            </option>

                            @foreach ($enderecos as $endereco)

                                <option
                                    value="{{ $endereco->id }}"
                                    {{ old('endereco_destino_id') == $endereco->id ? 'selected' : '' }}
                                >

                                    {{ $endereco->logradouro }},
                                    {{ $endereco->numero }}
                                    -
                                    {{ $endereco->bairro }},
                                    {{ $endereco->cidade }}/{{ $endereco->estado }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DIMENSÕES --}}

                    <div class="col-12 mt-2 mb-2">

                        <h5 class="mb-1">
                            Dimensões do produto
                        </h5>

                        <p class="text-muted small mb-3">
                            Informe as medidas em centímetros.
                        </p>

                    </div>


                    {{-- ALTURA --}}
                    <div class="col-md-4 mb-3">

                        <label for="altura" class="form-label">
                            Altura
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                id="altura"
                                name="altura"
                                class="form-control"
                                value="{{ old('altura') }}"
                                placeholder="0"
                                min="0"
                                step="0.01"
                                required
                            >

                            <span class="input-group-text">
                                cm
                            </span>

                        </div>

                    </div>


                    {{-- LARGURA --}}
                    <div class="col-md-4 mb-3">

                        <label for="largura" class="form-label">
                            Largura
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                id="largura"
                                name="largura"
                                class="form-control"
                                value="{{ old('largura') }}"
                                placeholder="0"
                                min="0"
                                step="0.01"
                                required
                            >

                            <span class="input-group-text">
                                cm
                            </span>

                        </div>

                    </div>


                    {{-- COMPRIMENTO --}}
                    <div class="col-md-4 mb-3">

                        <label for="comprimento" class="form-label">
                            Comprimento
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                id="comprimento"
                                name="comprimento"
                                class="form-control"
                                value="{{ old('comprimento') }}"
                                placeholder="0"
                                min="0"
                                step="0.01"
                                required
                            >

                            <span class="input-group-text">
                                cm
                            </span>

                        </div>

                    </div>


                    {{-- PESO --}}
                    <div class="col-md-4 mb-3">

                        <label for="peso" class="form-label">
                            Peso
                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                id="peso"
                                name="peso"
                                class="form-control"
                                value="{{ old('peso') }}"
                                placeholder="0"
                                min="0"
                                step="0.01"
                                required
                            >

                            <span class="input-group-text">
                                kg
                            </span>

                        </div>

                    </div>


                    {{-- DESCRIÇÃO --}}
                    <div class="col-12 mb-3">

                        <label for="descricao" class="form-label">
                            Descrição do produto
                        </label>

                        <textarea
                            id="descricao"
                            name="descricao"
                            class="form-control"
                            rows="4"
                            placeholder="Informe detalhes sobre o produto..."
                        >{{ old('descricao') }}</textarea>

                    </div>

                </div>


                {{-- BOTÕES --}}

                <div class="d-flex justify-content-end gap-2 mt-3">

                    <a
                        href="{{ route('empresa.dashboard') }}"
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        <i
                            data-feather="plus"
                            class="align-middle me-1"
                        ></i>

                        Cadastrar entrega

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection