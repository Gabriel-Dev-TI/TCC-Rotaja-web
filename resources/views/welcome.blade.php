@extends('layouts.guest')

@section('content')

<div class="container py-2">

    <div class="row align-items-center g-4">

        <div class="col-lg-5">

            <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-2 mt-3">
                Logística inteligente
            </span>

            <h1 class="display-5 fw-bold mb-2">
                Suas entregas,
                <span class="text-primary">
                    mais simples.
                </span>
            </h1>

            <p class="text-muted mb-3">
                O RotaJá conecta empresas e entregadores em uma única
                plataforma, tornando cada entrega mais rápida e organizada.
            </p>

            <div class="d-flex gap-2 mb-3">

                <a
                    href="{{ route('registro', ['tipo' => 'empresa']) }}"
                    class="btn btn-primary px-4"
                >
                    Sou uma empresa
                </a>

                <a
                    href="{{ route('registro', ['tipo' => 'entregador']) }}"
                    class="btn btn-outline-primary px-4"
                >
                    Sou entregador
                </a>

            </div>

            <div class="row g-2">

                <div class="col-4">

                    <i
                        data-feather="map-pin"
                        class="text-primary mb-1"
                    ></i>

                    <small class="d-block fw-semibold">
                        Rastreamento
                    </small>

                </div>

                <div class="col-4">

                    <i
                        data-feather="truck"
                        class="text-primary mb-1"
                    ></i>

                    <small class="d-block fw-semibold">
                        Entregadores
                    </small>

                </div>

                <div class="col-4">

                    <i
                        data-feather="shield"
                        class="text-primary mb-1"
                    ></i>

                    <small class="d-block fw-semibold">
                        Segurança
                    </small>

                </div>

            </div>

        </div>


        <div class="col-lg-7">

            {{-- VISÃO GERAL --}}

            <div class="card mb-3">

                <div class="card-body py-3 px-4">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <small class="text-muted">
                                Visão geral
                            </small>

                            <h4 class="mb-2">
                                Entregas
                            </h4>

                        </div>

                        <i
                            data-feather="activity"
                            class="text-primary"
                        ></i>

                    </div>


                    <div class="row">

                        <div class="col-4">

                            <small class="text-muted">
                                Pendentes
                            </small>

                            <h4 class="mb-0">
                                {{ $pendentes }}
                            </h4>

                        </div>


                        <div class="col-4">

                            <small class="text-muted">
                                Em trânsito
                            </small>

                            <h4 class="mb-0">
                                {{ $emTransito }}
                            </h4>

                        </div>


                        <div class="col-4">

                            <small class="text-muted">
                                Concluídas
                            </small>

                            <h4 class="mb-0">
                                {{ $concluidas }}
                            </h4>

                        </div>

                    </div>

                </div>

            </div>


            {{-- EMPRESAS / ENTREGADORES --}}

            <div class="row g-3 mb-3">

                <div class="col-md-6">

                    <div class="card h-100">

                        <div class="card-body py-3 px-4">

                            <i
                                data-feather="package"
                                class="text-primary mb-2"
                            ></i>

                            <h5 class="mb-1">
                                Empresas
                            </h5>

                            <p class="text-muted mb-0 small">
                                {{ $totalEmpresas }}
                                {{ $totalEmpresas == 1 ? 'empresa cadastrada' : 'empresas cadastradas' }}.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="card h-100">

                        <div class="card-body py-3 px-4">

                            <i
                                data-feather="navigation"
                                class="text-primary mb-2"
                            ></i>

                            <h5 class="mb-1">
                                Entregadores
                            </h5>

                            <p class="text-muted mb-0 small">
                                {{ $totalEntregadores }}
                                {{ $totalEntregadores == 1 ? 'entregador cadastrado' : 'entregadores cadastrados' }}.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RODAPÉ --}}

            <div class="card bg-primary border-0">

                <div class="card-body py-3 px-4 text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="text-white mb-1">
                                Tudo em um só lugar.
                            </h5>

                            <small>
                                Empresas e entregadores conectados
                                em uma única plataforma.
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection