@extends('layouts.app')

@section('title', 'Entregas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h1 class="h3 mb-1">
            Entregas
        </h1>

        <p class="text-muted mb-0">
            Gerencie todas as entregas do RotaJá.
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
            Entregas cadastradas
        </h5>

    </div>


    <div class="table-responsive">

        <table class="table table-hover my-0">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Produto</th>

                    <th class="d-none d-md-table-cell">
                        Empresa
                    </th>

                    <th class="d-none d-lg-table-cell">
                        Entregador
                    </th>

                    <th>Status</th>

                    <th>Preço</th>

                    <th class="d-none d-xl-table-cell">
                        Data
                    </th>

                    <th>Ações</th>

                </tr>

            </thead>


            <tbody>

                @forelse($entregas as $entrega)

                    @php

                        $statusCor = match($entrega->status) {

                            'pendente' => 'secondary',

                            'aceita' => 'info',

                            'em_transito' => 'warning',

                            'concluido' => 'success',

                            'cancelado' => 'danger',

                            default => 'light',

                        };


                        $statusLabel = match($entrega->status) {

                            'pendente' => 'Pendente',

                            'aceita' => 'Aceita',

                            'em_transito' => 'Em trânsito',

                            'concluido' => 'Concluída',

                            'cancelado' => 'Cancelada',

                            default => ucfirst($entrega->status),

                        };

                    @endphp


                    <tr>

                        <td>
                            {{ $entrega->id }}
                        </td>


                        <td>
                            {{ $entrega->nome_produto ?? '—' }}
                        </td>


                        <td class="d-none d-md-table-cell">

                            {{ $entrega->empresa->usuario->nome ?? '—' }}

                        </td>


                        <td class="d-none d-lg-table-cell">

                            {{ $entrega->entregador->usuario->nome ?? '—' }}

                        </td>


                        <td>

                            <span class="badge bg-{{ $statusCor }}">
                                {{ $statusLabel }}
                            </span>

                        </td>

                        <td>

                          R$ {{ number_format($entrega->preco,2,',','.') }} 
        
                        </td>


                        <td class="d-none d-xl-table-cell">

                            {{ $entrega->created_at?->format('d/m/Y') }}

                        </td>


                        <td>

                            <div class="d-flex gap-1">

                                <form
                                    action="{{ route('admin.entregas.destroy', $entrega) }}"
                                    method="POST"
                                    onsubmit="return confirm('Deseja realmente excluir esta entrega?');"
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
                            colspan="7"
                            class="text-center text-muted py-4"
                        >

                            Nenhuma entrega cadastrada.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection