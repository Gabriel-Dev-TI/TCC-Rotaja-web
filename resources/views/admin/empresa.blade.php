@extends('layouts.app')

@section('title', 'Empresas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h1 class="h3 mb-1">Empresas</h1>

        <p class="text-muted mb-0">
            Gerencie as empresas cadastradas no RotaJá.
        </p>
    </div>


</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-success m-1">

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show text-danger m-1">

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>
@endif

@if($errors->any())

    <div class="alert alert-danger text-danger m-1">

        <ul class="mb-0">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<div class="card">

    <div class="card-header">

        <h5 class="card-title mb-0">
            Empresas cadastradas
        </h5>

    </div>


    <div class="table-responsive">

        <table class="table table-hover my-0">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nome</th>

                    <th class="d-none d-md-table-cell">
                        E-mail
                    </th>

                    <th class="d-none d-lg-table-cell">
                        CNPJ
                    </th>

                    <th class="d-none d-xl-table-cell">
                        Cidade
                    </th>

                    <th>Ações</th>

                </tr>

            </thead>


            <tbody>

                @forelse($empresas as $empresa)

                    <tr>

                        <td>
                            {{ $empresa->id }}
                        </td>

                        <td>
                            {{ $empresa->usuario->nome ?? '—' }}
                        </td>

                        <td class="d-none d-md-table-cell">
                            {{ $empresa->usuario->email ?? '—' }}
                        </td>

                        <td class="d-none d-lg-table-cell">
                            {{ $empresa->cnpj ?? '—' }}
                        </td>

                        <td class="d-none d-xl-table-cell">
                            {{ $empresa->enderecos->first()?->cidade ?? '—' }}
                        </td>

                        <td>

                            <div class="d-flex gap-1">

                                <form
                                    action="{{ route('admin.empresas.destroy', $empresa) }}"
                                    method="POST"
                                    onsubmit="return confirm('Deseja realmente excluir esta empresa?');"
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
                            colspan="6"
                            class="text-center text-muted py-4"
                        >
                            Nenhuma empresa cadastrada.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection