@extends('layouts.app')

@section('content')

<div class="mb-4">

    <h1 class="h3 mb-1">
        Histórico
    </h1>

    <p class="text-muted mb-0">
        Confira suas entregas anteriores.
    </p>

</div>


<div class="card">

    <div class="card-header">

        <h5 class="card-title mb-0">
            Entregas
        </h5>

    </div>


    <div class="table-responsive">

        <table class="table table-hover my-0">

            <thead>

                <tr>

                    <th>
                        Produto
                    </th>

                    <th class="d-none d-xl-table-cell">
                        Data
                    </th>

                    <th class="d-none d-md-table-cell">
                        Empresa
                    </th>

                    <th>
                        Status
                    </th>

                    <th class="d-none d-xl-table-cell">
                        Entregador
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse ($ultimasEntregas as $entrega)

                    @php

                        $statusCor = match($entrega->status) {

                            'pendente' =>
                                'secondary',

                            'aceita' =>
                                'info',

                            'em_transito' =>
                                'warning',

                            'concluido' =>
                                'success',

                            'cancelado' =>
                                'danger',

                            default =>
                                'light',

                        };


                        $statusLabel = match($entrega->status) {

                            'pendente' =>
                                'Pendente',

                            'aceita' =>
                                'Aceita',

                            'em_transito' =>
                                'Em trânsito',

                            'concluido' =>
                                'Concluído',

                            'cancelado' =>
                                'Cancelado',

                            default =>
                                ucfirst($entrega->status),

                        };

                    @endphp


                    <tr>

                        <td>

                            <strong>
                                {{ $entrega->nome_produto }}
                            </strong>

                        </td>

                        <td class="d-none d-xl-table-cell">

                            {{ $entrega->created_at->format('d/m/Y') }}

                        </td>

                        <td class="d-none d-md-table-cell">

                            {{ $entrega->empresa->usuario->nome ?? '—' }}

                        </td>

                        <td>

                            <span
                                class="badge bg-{{ $statusCor }}"
                            >
                                {{ $statusLabel }}
                            </span>

                        </td>

                        <td class="d-none d-xl-table-cell">

                            {{ $entrega->entregador->usuario->nome ?? '—' }}

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-4"
                        >

                            <div class="mb-2">

                                <i
                                    data-feather="inbox"
                                    style="width:40px;height:40px;"
                                ></i>

                            </div>

                            Nenhuma entrega encontrada.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection

