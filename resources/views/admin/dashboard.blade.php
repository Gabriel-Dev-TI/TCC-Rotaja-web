@extends('layouts.app')

@section('title', 'Painel Admin')

@section('content')

<h1 class="h3 mb-3">
    Painel de <strong>Análise</strong>
</h1>

<div class="row">

    <div class="col-xl-6 col-xxl-5 d-flex">

        <div class="w-100">

            <div class="row">

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
                                    Total de entregas cadastradas
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Contas criadas
                                    </h5>

                                </div>

                                <div class="col-auto">

                                    <div class="stat text-primary">

                                        <i
                                            class="align-middle"
                                            data-feather="users"
                                        ></i>

                                    </div>

                                </div>

                            </div>

                            <h1 class="mt-1 mb-3">
                                {{ $totalEmpresas + $totalEntregadores }}
                            </h1>

                            <div class="mb-0">

                                <span class="text-muted">
                                    Empresas + entregadores
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-sm-6">

                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Empresas
                                    </h5>

                                </div>

                                <div class="col-auto">

                                    <div class="stat text-primary">

                                        <i
                                            class="align-middle"
                                            data-feather="briefcase"
                                        ></i>

                                    </div>

                                </div>

                            </div>

                            <h1 class="mt-1 mb-3">
                                {{ $totalEmpresas }}
                            </h1>

                            <div class="mb-0">

                                <span class="text-muted">
                                    Total de empresas cadastradas
                                </span>

                            </div>

                        </div>

                    </div>


                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Entregadores
                                    </h5>

                                </div>

                                <div class="col-auto">

                                    <div class="stat text-primary">

                                        <i
                                            class="align-middle"
                                            data-feather="user-check"
                                        ></i>

                                    </div>

                                </div>

                            </div>

                            <h1 class="mt-1 mb-3">
                                {{ $totalEntregadores }}
                            </h1>

                            <div class="mb-0">

                                <span class="text-muted">
                                    Total de entregadores cadastrados
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

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

<div class="row">

    <div class="col-12 col-lg-8 col-xxl-9 d-flex">

        <div class="card flex-fill">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Últimas Entregas
                </h5>

            </div>


            <div class="table-responsive">

                <table class="table table-hover my-0">

                    <thead>

                        <tr>

                            <th>
                                Nome
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

    </div>

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
@push('scripts')

<script>

document.addEventListener("DOMContentLoaded", function () {

    const meses = [
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
    ];

    const entregasMensais = @json($entregasMensais);

    const lineCanvas =
        document.getElementById("chartjs-dashboard-line");

    if (lineCanvas) {

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


        new Chart(
            lineCanvas,
            {
                type: "line",

                data: {

                    labels: meses,

                    datasets: [

                        {
                            label: "Entregas",

                            fill: true,

                            backgroundColor:
                                gradient,

                            borderColor:
                                window.theme?.primary ??
                                '#6D4AFF',

                            data:
                                entregasMensais,

                            tension: 0.3

                        }

                    ]

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
                                color:
                                    "rgba(0,0,0,0)"
                            }

                        },

                        y: {

                            beginAtZero: true,

                            ticks: {
                                precision: 0
                            },

                            grid: {

                                color:
                                    "rgba(0,0,0,0.0)",

                                borderDash:
                                    [3, 3]

                            }

                        }

                    }

                }

            }
        );

    }

    const barCanvas =
        document.getElementById("chartjs-dashboard-bar");

    if (barCanvas) {

        new Chart(
            barCanvas,
            {

                type: "bar",

                data: {

                    labels: meses,

                    datasets: [

                        {

                            label:
                                "Entregas",

                            backgroundColor:
                                window.theme?.primary ??
                                '#6D4AFF',

                            borderColor:
                                window.theme?.primary ??
                                '#6D4AFF',

                            hoverBackgroundColor:
                                window.theme?.primary ??
                                '#6D4AFF',

                            hoverBorderColor:
                                window.theme?.primary ??
                                '#6D4AFF',

                            data:
                                entregasMensais,

                            barPercentage:
                                .75,

                            categoryPercentage:
                                .5

                        }

                    ]

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
                                color:
                                    "transparent"
                            }

                        }

                    }

                }

            }
        );

    }

});

</script>

@endpush

@endsection