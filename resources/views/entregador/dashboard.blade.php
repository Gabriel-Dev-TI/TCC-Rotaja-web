@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="h3 mb-1">
            Painel de <strong>Entregas</strong>
        </h1>

        <p class="text-muted mb-0">
            Visualize e gerencie as entregas disponíveis.
        </p>

    </div>

</div>

<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Entregas disponíveis
                </h5>

            </div>


            <div class="table-responsive">

                <table class="table table-hover my-0">

                    <thead>

                        <tr>

                            <th>
                                Produto
                            </th>

                            <th class="d-none d-md-table-cell">
                                Empresa
                            </th>

                            <th>
                                Distância
                            </th>

                            <th>
                                Preço
                            </th>

                            <th class="d-none d-xl-table-cell">
                                Data
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Ação
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($entregas as $entrega)

                            <tr>

                                <td>

                                    {{ $entrega->nome_produto ?? 'Entrega' }}

                                </td>

                                <td class="d-none d-md-table-cell">

                                    {{ $entrega->empresa?->usuario?->nome
                                        ?? 'Empresa' }}

                                </td>

                                <td>

                                    @if($entrega->distancia !== null)

                                        {{ number_format(
                                            $entrega->distancia,
                                            1,
                                            ',',
                                            '.'
                                        ) }}
                                        km

                                    @else

                                        -

                                    @endif

                                </td>

                                <td>

                                    @if($entrega->preco !== null)

                                        R$
                                        {{ number_format(
                                            $entrega->preco,
                                            2,
                                            ',',
                                            '.'
                                        ) }}

                                    @else

                                        -

                                    @endif

                                </td>

                                <td class="d-none d-xl-table-cell">

                                    {{ $entrega->created_at?->format('d/m/Y') }}

                                </td>

                                <td>

                                    @switch($entrega->status)

                                        @case('pendente')

                                            <span class="badge bg-warning">
                                                Pendente
                                            </span>

                                            @break


                                        @case('aceita')

                                            <span class="badge bg-info">
                                                Aceita
                                            </span>

                                            @break


                                        @case('em_transito')

                                            <span class="badge bg-primary">
                                                Em trânsito
                                            </span>

                                            @break


                                        @case('concluido')

                                            <span class="badge bg-success">
                                                Concluída
                                            </span>

                                            @break


                                        @case('cancelado')

                                            <span class="badge bg-danger">
                                                Cancelada
                                            </span>

                                            @break


                                        @default

                                            <span class="badge bg-secondary">

                                                {{ ucfirst($entrega->status) }}

                                            </span>

                                    @endswitch

                                </td>

                                <td>

                                    @if($entrega->status === 'pendente')

                                        @if(
                                            !$entrega->entregador_id ||
                                            $entrega->entregador_id === auth()->user()->entregador?->id
                                        )

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'entregador.entrega.aceitar',
                                                    $entrega->id
                                                ) }}"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="btn btn-success btn-sm"
                                                >

                                                    <i
                                                        data-feather="check"
                                                        class="align-middle me-1"
                                                    ></i>

                                                    Aceitar

                                                </button>

                                            </form>

                                        @else

                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                disabled
                                            >

                                                Indisponível

                                            </button>

                                        @endif


                                    @elseif(
                                        $entrega->status === 'aceita' ||
                                        $entrega->status === 'em_transito'
                                    )

                                        @if(
                                            $entrega->entregador_id ===
                                            auth()->user()->entregador?->id
                                        )

                                            <a
                                                href="{{ route(
                                                    'rota',
                                                    $entrega->id
                                                ) }}"
                                                class="btn btn-primary btn-sm"
                                            >

                                                <i
                                                    data-feather="map"
                                                    class="align-middle me-1"
                                                ></i>

                                                Ir para rota

                                            </a>

                                        @else

                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                disabled
                                            >

                                                Indisponível

                                            </button>

                                        @endif


                                    @elseif($entrega->status === 'concluido')

                                        <span class="text-muted small">
                                            Finalizada
                                        </span>


                                    @elseif($entrega->status === 'cancelado')

                                        <span class="text-muted small">
                                            Cancelada
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-5"
                                >

                                    <div>

                                        <i
                                            data-feather="package"
                                            style="
                                                width: 45px;
                                                height: 45px;
                                            "
                                            class="mb-3"
                                        ></i>

                                        <h5>
                                            Nenhuma entrega disponível
                                        </h5>

                                        <p class="mb-0">
                                            No momento não existem entregas
                                            disponíveis para aceitar.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

    <div class="col-12 d-flex">

        <div class="card flex-fill w-100">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Entregas realizadas
                </h5>

            </div>


            <div class="card-body">

                <div
                    style="
                        position: relative;
                        height: 300px;
                    "
                >

                    <canvas
                        id="chartjs-entregas"
                    ></canvas>

                </div>

            </div>

        </div>

    </div>

</div>


@endsection

@push('scripts')

<script>

document.addEventListener("DOMContentLoaded", function () {

    const canvas =
        document.getElementById('chartjs-entregas');

    if (!canvas) {
        return;
    }


    new Chart(canvas, {

        type: 'line',

        data: {

            labels: [
                'Jan',
                'Fev',
                'Mar',
                'Abr',
                'Mai',
                'Jun',
                'Jul',
                'Ago',
                'Set',
                'Out',
                'Nov',
                'Dez'
            ],

            datasets: [{

                label: 'Entregas concluídas',

                data: @json($dadosMensais),

                borderColor:
                    window.theme?.primary,

                backgroundColor:
                    'transparent',

                tension: 0.3,

                borderWidth: 2,

                pointRadius: 4,

                pointHoverRadius: 6

            }]

        },


        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {

                    beginAtZero: true,

                    min: 0,

                    ticks: {

                        precision: 0

                    }

                }

            },


            plugins: {

                legend: {

                    display: false

                }

            }

        }

    });

});

</script>

@endpush