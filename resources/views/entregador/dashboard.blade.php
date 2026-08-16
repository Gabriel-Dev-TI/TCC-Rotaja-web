@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h3 mb-1">
            Painel de <strong>Entregas</strong>
        </h1>

        <p class="text-muted mb-0">
            Olá, {{ $usuario->nome }}.
        </p>
    </div>

</div>


{{-- =========================================================
    ENTREGAS DISPONÍVEIS
========================================================= --}}

<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Entregas Disponíveis
                </h5>

            </div>


            <div class="table-responsive">

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
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($entregas as $entrega)

                            <tr>

                                {{-- PRODUTO --}}
                                <td>

                                    {{ $entrega->nome ?? $entrega->produto ?? 'Entrega' }}

                                </td>


                                {{-- DATA --}}
                                <td class="d-none d-xl-table-cell">

                                    {{ $entrega->created_at?->format('d/m/Y') }}

                                </td>


                                {{-- EMPRESA --}}
                                <td class="d-none d-md-table-cell">

                                    {{ $entrega->empresa?->usuario?->nome ?? 'Empresa' }}

                                </td>


                                {{-- STATUS --}}
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

                                            <span class="badge bg-warning">
                                                Em trânsito
                                            </span>

                                            @break


                                        @case('concluido')

                                            <span class="badge bg-success">
                                                Concluída
                                            </span>

                                            @break


                                        @default

                                            <span class="badge bg-secondary">
                                                {{ ucfirst($entrega->status) }}
                                            </span>

                                    @endswitch

                                </td>


                                {{-- AÇÃO --}}
                                <td class="d-none d-xl-table-cell">

                                    @if($entrega->status === 'pendente')

                                        <form
                                            method="POST"
                                            action="{{ route('entregador.entrega.aceitar', $entrega->id) }}"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-success btn-sm"
                                            >
                                                Aceitar
                                            </button>

                                        </form>

                                    @elseif($entrega->status === 'aceita')

                                        <span class="btn btn-secondary btn-sm">
                                            Aceita
                                        </span>

                                    @elseif($entrega->status === 'em_transito')

                                        <span class="btn btn-secondary btn-sm">
                                            Em andamento
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >

                                    Nenhuma entrega disponível.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    ESTATÍSTICAS
========================================================= --}}

<div class="row">


    {{-- ENTREGAS --}}
    <div class="col-xl-6 col-xxl-6 d-flex">

        <div class="w-100">

            <div class="row">


                <div class="col-12">

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



                {{-- ENTREGAS CONCLUÍDAS --}}
                <div class="col-12">

                    <div class="card">

                        <div class="card-body">

                            <div class="row">

                                <div class="col mt-0">

                                    <h5 class="card-title">
                                        Entregas concluídas
                                    </h5>

                                </div>


                                <div class="col-auto">

                                    <div class="stat text-success">

                                        <i
                                            class="align-middle"
                                            data-feather="check-circle"
                                        ></i>

                                    </div>

                                </div>

                            </div>


                            <h1 class="mt-1 mb-3">
                                {{ $entregasConcluidas->count() }}
                            </h1>


                            <div class="mb-0">

                                <span class="text-muted">
                                    Entregas finalizadas
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
        RESUMO
    ====================================================== --}}

    <div class="col-xl-6 col-xxl-6">

        <div class="card flex-fill w-100">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Resumo das entregas
                </h5>

            </div>


            <div class="card-body">

                <div class="mb-4">

                    <div class="d-flex justify-content-between mb-2">

                        <span>
                            Pendentes
                        </span>

                        <strong>
                            {{ $entregasDisponiveis->count() }}
                        </strong>

                    </div>

                    <div class="progress">

                        <div
                            class="progress-bar bg-warning"
                            role="progressbar"
                            style="width: {{ $totalEntregas > 0 ? ($entregasDisponiveis->count() / $totalEntregas) * 100 : 0 }}%"
                        ></div>

                    </div>

                </div>



                <div class="mb-4">

                    <div class="d-flex justify-content-between mb-2">

                        <span>
                            Em andamento
                        </span>

                        <strong>
                            {{ $entregasAceitas->count() }}
                        </strong>

                    </div>

                    <div class="progress">

                        <div
                            class="progress-bar bg-info"
                            role="progressbar"
                            style="width: {{ $totalEntregas > 0 ? ($entregasAceitas->count() / $totalEntregas) * 100 : 0 }}%"
                        ></div>

                    </div>

                </div>



                <div>

                    <div class="d-flex justify-content-between mb-2">

                        <span>
                            Concluídas
                        </span>

                        <strong>
                            {{ $entregasConcluidas->count() }}
                        </strong>

                    </div>

                    <div class="progress">

                        <div
                            class="progress-bar bg-success"
                            role="progressbar"
                            style="width: {{ $totalEntregas > 0 ? ($entregasConcluidas->count() / $totalEntregas) * 100 : 0 }}%"
                        ></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

    if (typeof feather !== 'undefined') {
        feather.replace();
    }

</script>

@endpush