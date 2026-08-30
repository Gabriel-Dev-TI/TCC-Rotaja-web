@extends('layouts.app')

@section('content')

<div class="container-fluid p-0">

@if(!$entrega)

    <div class="card">
        <div class="card-body text-center py-5">

            <i data-feather="map" class="text-muted mb-3" style="width:48px;height:48px;"></i>

            <h4>Nenhuma rota em andamento</h4>

            <p class="text-muted mb-4">
                Você não possui nenhuma entrega aceita ou em trânsito.
            </p>

            <a href="{{ route('entregador.dashboard') }}" class="btn btn-primary">
                <i data-feather="arrow-left" class="me-1"></i>
                Voltar para entregas
            </a>

        </div>
    </div>

@else

    @php
        $origem = $entrega->enderecoOrigem;
        $destino = $entrega->enderecoDestino;
    @endphp

    <div class="row g-3">

        <div class="col-12 col-xl-8">

            <div class="card mb-0">

                <div class="card-body p-0">

                    <div
                        id="map"
                        style="height: calc(100vh - 100px); min-height: 500px;"
                    ></div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="card mb-0">

                <div class="card-header">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <h4 class="card-title mb-1">
                                {{ $entrega->nome_produto }}
                            </h4>

                            <div class="text-muted small">
                                {{ $entrega->empresa->usuario->nome ?? 'Empresa' }}
                            </div>

                        </div>

                        <span class="badge bg-{{ $entrega->status === 'em_transito' ? 'warning' : 'info' }}">

                            {{ $entrega->status === 'em_transito' ? 'Em trânsito' : 'Aceita' }}

                        </span>

                    </div>

                </div>


                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-4">

                            <div class="text-muted small">
                                Preço
                            </div>

                            <strong>
                                R$ {{ number_format($entrega->preco, 2, ',', '.') }}
                            </strong>

                        </div>

                        <div class="col-4">

                            <div class="text-muted small">
                                Distância
                            </div>

                            <strong id="distancia">

                                {{ number_format($entrega->distancia ?? 0, 1, ',', '.') }} km

                            </strong>

                        </div>

                        <div class="col-4">

                            <div class="text-muted small">
                                Estimativa
                            </div>

                            <strong id="tempo">

                                @if($entrega->tempo_estimado_minutos)
                                    {{ $entrega->tempo_estimado_minutos }} min
                                @else
                                    —
                                @endif

                            </strong>

                        </div>

                    </div>


                    <hr>

                    <div class="mb-4">

                        <div class="d-flex align-items-center mb-2">

                            <i data-feather="package" class="text-primary me-2"></i>

                            <strong>
                                Local de retirada
                            </strong>

                        </div>

                        @if($origem)

                            <div class="small">

                                <strong>CEP:</strong>
                                {{ $origem->cep }}

                                <br>

                                {{ $origem->logradouro }},
                                {{ $origem->numero }}

                                @if($origem->complemento)
                                    - {{ $origem->complemento }}
                                @endif

                                <br>

                                {{ $origem->bairro }},
                                {{ $origem->cidade }} -
                                {{ $origem->estado }}

                            </div>

                        @else

                            <div class="text-muted small">
                                Endereço de retirada não disponível.
                            </div>

                        @endif

                    </div>

                    <div class="mb-4">

                        <div class="d-flex align-items-center mb-2">

                            <i data-feather="map-pin" class="text-primary me-2"></i>

                            <strong>
                                Local de destino
                            </strong>

                        </div>

                        @if($destino)

                            <div class="small">

                                <strong>CEP:</strong>
                                {{ $destino->cep }}

                                <br>

                                {{ $destino->logradouro }},
                                {{ $destino->numero }}

                                @if($destino->complemento)
                                    - {{ $destino->complemento }}
                                @endif

                                <br>

                                {{ $destino->bairro }},
                                {{ $destino->cidade }} -
                                {{ $destino->estado }}

                            </div>

                        @else

                            <div class="text-muted small">
                                Endereço de destino não disponível.
                            </div>

                        @endif

                    </div>

                    @if($entrega->descricao)

                        <div class="mb-4">

                            <div class="text-muted small mb-1">
                                Descrição
                            </div>

                            <div class="small">
                                {{ $entrega->descricao }}
                            </div>

                        </div>

                    @endif


                    <form
                        method="POST"
                        action="{{ route('entrega.observacao', $entrega->id) }}"
                    >

                        @csrf

                        <div class="mb-2">

                            <label
                                for="observacoes"
                                class="form-label"
                            >
                                Observação
                            </label>

                            <textarea
                                name="observacoes"
                                id="observacoes"
                                class="form-control"
                                rows="2"
                                maxlength="1000"
                                placeholder="Digite uma observação..."
                            >{{ old('observacoes', $entrega->observacoes) }}</textarea>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-outline-primary btn-sm"
                        >

                            <i data-feather="message-square" class="me-1"></i>

                            Registrar observação

                        </button>

                    </form>

                </div>

                <div class="card-footer">

                    <div class="d-grid gap-2">

                        <form
                            method="POST"
                            action="{{ route('entrega.finalizar', $entrega->id) }}"
                        >

                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-success w-100"
                                onclick="return confirm('Deseja finalizar esta entrega?')"
                            >

                                <i data-feather="check-circle" class="me-1"></i>

                                Finalizar entrega

                            </button>

                        </form>


                        <form
                            method="POST"
                            action="{{ route('entrega.cancelar', $entrega->id) }}"
                            onsubmit="return confirm('Deseja realmente cancelar esta entrega?')"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="btn btn-outline-danger w-100"
                            >

                                <i data-feather="x-circle" class="me-1"></i>

                                Cancelar entrega

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endif

</div>

@endsection

@if($entrega && $entrega->enderecoOrigem && $entrega->enderecoDestino)

@push('scripts')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const origem = [
        {{ $entrega->enderecoOrigem->latitude }},
        {{ $entrega->enderecoOrigem->longitude }}
    ];

    const destino = [
        {{ $entrega->enderecoDestino->latitude }},
        {{ $entrega->enderecoDestino->longitude }}
    ];


    const map = L.map('map');


    L.tileLayer(
        'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }
    ).addTo(map);


    L.marker(origem)
        .addTo(map)
        .bindPopup('Local de retirada');


    L.marker(destino)
        .addTo(map)
        .bindPopup('Local de destino');


    // Mudar para o MapBox..
    const url =
        'https://router.project-osrm.org/route/v1/driving/' +
        `${origem[1]},${origem[0]};` +
        `${destino[1]},${destino[0]}` +
        '?overview=full&geometries=geojson';


    fetch(url).then(response => {

            if (!response.ok) {
                throw new Error('Erro ao buscar rota.');
            }

            return response.json();

    }).then(data => {

            if (!data.routes || !data.routes.length) {
                throw new Error('Rota não encontrada.');
            }


            const rota = data.routes[0];


            const distancia = rota.distance / 1000;


            document.getElementById('distancia').textContent =
            distancia.toFixed(1).replace('.', ',') + ' km';

            const minutos = Math.round(rota.duration / 60);

            let tempo;


            if (minutos < 60) {
                tempo = minutos + ' min';
            } else {

                const horas = Math.floor(minutos / 60);
                const min = minutos % 60;
                tempo = horas + 'h ' + min + 'min';

            }


            document.getElementById('tempo').textContent = tempo;


            const pontos =
                rota.geometry.coordinates.map(ponto => [
                    ponto[1],
                    ponto[0]
                ]);


            const linha = L.polyline(
                pontos,
                {
                    color: '#6D4AFF',
                    weight: 5,
                    opacity: 0.9
                }
            ).addTo(map);


            map.fitBounds(
                linha.getBounds(),
                {
                    padding: [30, 30],
                    maxZoom: 16
                }
            );

        })

        .catch(error => {

            console.error(error);

            map.fitBounds(
                L.latLngBounds([origem, destino]),
                {
                    padding: [30, 30],
                    maxZoom: 15
                }
            );

        });


    setTimeout(function () {

        map.invalidateSize();

    }, 300);

});

</script>

@endpush

@endif
