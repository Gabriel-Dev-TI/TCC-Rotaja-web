@extends('layouts.app')

@section('title', 'Painel da Empresa')

@section('content')

<h1 class="h3 mb-3">
    Painel de <strong>Entregas</strong>
</h1>

<div class="row">

    {{-- ENTREGAS --}}
    <div class="col-xl-6 col-xxl-5 d-flex">

        <div class="w-100">

            <div class="row">

                {{-- TOTAL --}}
                <div class="col-sm-6">

                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Entregas
                                    </h5>

                                </div>

                                <div class="col-auto">

                                    <div class="stat text-primary">

                                        <i
                                            class="align-middle"
                                            data-feather="truck"
                                        ></i>

                                    </div>

                                </div>

                            </div>

                            <h1 class="mt-1 mb-3">
                                {{ $totalEntregas }}
                            </h1>

                            <div class="mb-0">

                                <span class="text-muted">
                                    Total de entregas
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- PENDENTES --}}
                <div class="col-sm-6">

                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Pendentes
                                    </h5>

                                </div>

                                <div class="col-auto">

                                    <div class="stat text-primary">

                                        <i
                                            class="align-middle"
                                            data-feather="clock"
                                        ></i>

                                    </div>

                                </div>

                            </div>

                            <h1 class="mt-1 mb-3">
                                {{ $entregasPendentes }}
                            </h1>

                            <div class="mb-0">

                                <span class="text-muted">
                                    Aguardando entregador
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ANDAMENTO --}}
                <div class="col-sm-6">

                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Em andamento
                                    </h5>

                                </div>

                                <div class="col-auto">

                                    <div class="stat text-primary">

                                        <i
                                            class="align-middle"
                                            data-feather="truck"
                                        ></i>

                                    </div>

                                </div>

                            </div>

                            <h1 class="mt-1 mb-3">
                                {{ $entregasAndamento }}
                            </h1>

                            <div class="mb-0">

                                <span class="text-muted">
                                    Entregas em andamento
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- CONCLUÍDAS --}}
                <div class="col-sm-6">

                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Concluídas
                                    </h5>

                                </div>

                                <div class="col-auto">

                                    <div class="stat text-primary">

                                        <i
                                            class="align-middle"
                                            data-feather="check-circle"
                                        ></i>

                                    </div>

                                </div>

                            </div>

                            <h1 class="mt-1 mb-3">
                                {{ $entregasConcluidas }}
                            </h1>

                            <div class="mb-0">

                                <span class="text-muted">
                                    Entregas concluídas
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- GRÁFICO DE MOVIMENTAÇÕES --}}
    <div class="col-xl-6 col-xxl-7">

        <div class="card flex-fill w-100">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Movimentações recentes
                </h5>

            </div>

            <div class="card-body py-3">

                <div
                    class="chart chart-sm"
                    style="position: relative; height: 260px;"
                >

                    <canvas id="chartjs-dashboard-line"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- PARTE INFERIOR --}}

<div class="row">

    {{-- ÚLTIMAS ENTREGAS --}}
    <div class="col-12 col-lg-8 col-xxl-9 d-flex">

        <div class="card flex-fill">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Últimas entregas
                </h5>

            </div>

            <table class="table table-hover my-0">

                <thead>

                    <tr>

                        <th>Nome</th>

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
                                {{ $entrega->nome_produto }}
                            </td>

                            <td class="d-none d-xl-table-cell">

                                {{ $entrega->created_at->format('d/m/Y') }}

                            </td>

                            <td class="d-none d-md-table-cell">

                                {{ $entrega->empresa->usuario->nome ?? '—' }}

                            </td>

                            <td>

                                <span class="badge bg-{{ $statusCor }}">

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
                                class="text-center text-muted"
                            >

                                Nenhuma entrega encontrada.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ENTREGAS MENSAIS --}}
    <div class="col-12 col-lg-4 col-xxl-3 d-flex">

        <div class="card flex-fill w-100">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Entregas Mensais
                </h5>

            </div>

            <div class="card-body d-flex w-100">

                <div
                    class="align-self-center chart chart-lg"
                    style="
                        position: relative;
                        height: 220px;
                        width: 100%;
                    "
                >

                    <canvas id="chartjs-dashboard-bar"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener("DOMContentLoaded", function () {


    /*
     * GRÁFICO DE MOVIMENTAÇÕES
     */

    const lineCanvas =
        document.getElementById(
            "chartjs-dashboard-line"
        );


    const ctx =
        lineCanvas.getContext("2d");


    const gradient =
        ctx.createLinearGradient(
            0,
            0,
            0,
            225
        );


    gradient.addColorStop(
        0,
        "rgba(215, 227, 244, 1)"
    );


    gradient.addColorStop(
        1,
        "rgba(215, 227, 244, 0)"
    );


    new Chart(lineCanvas, {

        type: "line",

        data: {

            labels: [
                "Jan",
                "Fev",
                "Mar",
                "Abr",
                "Mai",
                "Jun",
                "Jul",
                "Ago",
                "Set",
                "Out",
                "Nov",
                "Dez"
            ],

            datasets: [{

                label: "Entregas",

                fill: true,

                backgroundColor: gradient,

                borderColor:
                    window.theme?.primary
                    ?? '#6D4AFF',

                data: @json($dadosMensais)

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                },

                filler: {
                    propagate: false
                }

            },

            scales: {

                x: {

                    grid: {
                        color: "rgba(0,0,0,0.0)"
                    }

                },

                y: {

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    },

                    grid: {
                        color: "rgba(0,0,0,0.0)"
                    }

                }

            }

        }

    });


    /*
     * GRÁFICO DE ENTREGAS MENSAIS
     */

    const barCanvas =
        document.getElementById(
            "chartjs-dashboard-bar"
        );


    new Chart(barCanvas, {

        type: "bar",

        data: {

            labels: [
                "Jan",
                "Fev",
                "Mar",
                "Abr",
                "Mai",
                "Jun",
                "Jul",
                "Ago",
                "Set",
                "Out",
                "Nov",
                "Dez"
            ],

            datasets: [{

                label: "Este ano",

                backgroundColor:
                    window.theme?.primary
                    ?? '#6D4AFF',

                borderColor:
                    window.theme?.primary
                    ?? '#6D4AFF',

                hoverBackgroundColor:
                    window.theme?.primary
                    ?? '#6D4AFF',

                hoverBorderColor:
                    window.theme?.primary
                    ?? '#6D4AFF',

                data: @json($dadosMensais),

                barPercentage: .75,

                categoryPercentage: .5

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    grid: {
                        display: false
                    },

                    ticks: {
                        precision: 0
                    }

                },

                x: {

                    grid: {
                        color: "transparent"
                    }

                }

            }

        }

    });

});

</script>

@endpush