@extends('layouts.app')

@section('title', 'Entregadores')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-1">Entregadores</h1>
        <p class="text-muted mb-0">
            Gerencie os entregadores cadastrados no RotaJá.
        </p>
    </div>

</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-success m-1">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show text-danger m-1">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
        <h5 class="card-title mb-0">Entregadores cadastrados</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover my-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th class="d-none d-md-table-cell">E-mail</th>
                    <th class="d-none d-lg-table-cell">CPF</th>
                    <th class="d-none d-lg-table-cell">Veículo</th>
                    <th class="d-none d-xl-table-cell">Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                @forelse($entregadores as $entregador)

                    <tr>

                        <td>
                            {{ $entregador->id }}
                        </td>

                        <td>
                            {{ $entregador->usuario->nome ?? '—' }}
                        </td>

                        <td class="d-none d-md-table-cell">
                            {{ $entregador->usuario->email ?? '—' }}
                        </td>

                        <td class="d-none d-lg-table-cell">
                            {{ $entregador->cpf ?? '—' }}
                        </td>

                        <td class="d-none d-lg-table-cell">
                            {{ $entregador->tipo_veiculo ?? '—' }}
                        </td>

                        <td class="d-none d-xl-table-cell">
                            {{ $entregador->usuario->telefone ?? '—' }}
                        </td>

                        <td>
                            <div class="d-flex gap-1">

                                <form
                                    action="{{ route('admin.entregadores.destroy', $entregador) }}"
                                    method="POST"
                                    onsubmit="return confirm('Deseja realmente excluir este entregador?');"
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
                        <td colspan="7" class="text-center text-muted py-4">
                            Nenhum entregador cadastrado.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>
</div>

@endsection