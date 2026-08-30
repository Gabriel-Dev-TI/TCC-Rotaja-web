@extends('layouts.app')

@section('title', 'Endereços')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h1 class="h3 mb-1">
            Endereços
        </h1>

        <p class="text-muted mb-0">
            Gerencie os endereços utilizados pela empresa.
        </p>

    </div>


    <a
        href="{{ route('empresa.enderecos.create') }}"
        class="btn btn-primary d-flex align-items-center gap-2"
    >

        <i data-feather="plus"></i>

        Novo endereço

    </a>

</div>

@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show m-1 text-success">

        <i data-feather="check-circle" class="me-1"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


@if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show m-1 text-danger">

        <i data-feather="alert-circle" class="me-1"></i>

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


<div class="card mb-3">

    <div class="card-header">

        <h5 class="card-title mb-0">
            Endereço da empresa
        </h5>

    </div>


    <div class="card-body">

        @if($enderecoEmpresa)

            <div class="d-flex justify-content-between align-items-start">

                <div>

                    <div class="d-flex align-items-center mb-2">

                        <i
                            data-feather="home"
                            class="text-primary me-2"
                        ></i>

                        <strong>
                            Endereço principal
                        </strong>

                    </div>


                    <div>

                        {{ $enderecoEmpresa->logradouro }},
                        {{ $enderecoEmpresa->numero }}

                        <br>

                        {{ $enderecoEmpresa->bairro }}

                        <br>

                        {{ $enderecoEmpresa->cidade }} -
                        {{ $enderecoEmpresa->estado }}

                        <br>

                        CEP:
                        {{ $enderecoEmpresa->cep }}

                        @if($enderecoEmpresa->complemento)

                            <br>

                            {{ $enderecoEmpresa->complemento }}

                        @endif

                    </div>

                </div>


                <span class="badge bg-primary">
                    Principal
                </span>

            </div>

        @else

            <div class="text-center text-muted py-3">

                <i
                    data-feather="map-pin"
                    style="width:40px;height:40px;"
                    class="mb-2"
                ></i>

                <p class="mb-0">
                    A empresa ainda não possui um endereço cadastrado.
                </p>

            </div>

        @endif

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h5 class="card-title mb-0">
            Endereços cadastrados
        </h5>

    </div>


    <div class="table-responsive">

        <table class="table table-hover my-0">

            <thead>

                <tr>

                    <th>
                        Endereço
                    </th>

                    <th class="d-none d-md-table-cell">
                        Bairro
                    </th>

                    <th class="d-none d-lg-table-cell">
                        Cidade
                    </th>

                    <th class="d-none d-xl-table-cell">
                        CEP
                    </th>

                    <th class="text-end">
                        Ações
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($enderecos as $endereco)

                    <tr>

                        <td>

                            <div class="fw-semibold">

                                {{ $endereco->logradouro }},
                                {{ $endereco->numero }}

                            </div>

                            @if($endereco->complemento)

                                <small class="text-muted">

                                    {{ $endereco->complemento }}

                                </small>

                            @endif

                        </td>


                        <td class="d-none d-md-table-cell">

                            {{ $endereco->bairro }}

                        </td>


                        <td class="d-none d-lg-table-cell">

                            {{ $endereco->cidade }} -
                            {{ $endereco->estado }}

                        </td>


                        <td class="d-none d-xl-table-cell">

                            {{ $endereco->cep }}

                        </td>


                        <td class="text-end">

                            <div class="d-flex justify-content-end gap-1">

                                <a
                                    href="{{ route('empresa.enderecos.edit', $endereco) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Editar"
                                >

                                    <i data-feather="edit-2"></i>

                                </a>


                                <form
                                    method="POST"
                                    action="{{ route('empresa.enderecos.destroy', $endereco) }}"
                                    onsubmit="return confirm('Deseja realmente excluir este endereço?')"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Excluir"
                                    >

                                        <i data-feather="trash-2"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-4"
                        >

                            <i
                                data-feather="map-pin"
                                style="width:40px;height:40px;"
                                class="mb-2"
                            ></i>

                            <div>
                                Nenhum endereço cadastrado.
                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection