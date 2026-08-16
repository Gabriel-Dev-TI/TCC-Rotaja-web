@extends('layouts.app')

@section('content')

<style>
    html,
    body {
        overflow: hidden !important;
    }

    .rota-container {
        position: relative;
        width: 100%;
        height: calc(100vh - 70px);
        overflow: hidden;
    }

    #map {
        position: absolute;
        top: 0;
        left: 0;
        width: calc(100% - 380px);
        height: 100%;
    }

    .rota-card {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 340px;
        height: calc(100% - 40px);
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .15);
        z-index: 1000;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .rota-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #eee;
        flex-shrink: 0;
    }

    .rota-card-body {
        padding: 18px 20px;
        flex: 1;
        overflow: hidden;
    }

    .info-item {
        margin-bottom: 14px;
    }

    .info-label {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 3px;
    }

    .info-value {
        font-size: 14px;
        font-weight: 500;
    }

    .rota-card-footer {
        padding: 15px 20px 20px;
        border-top: 1px solid #eee;
        flex-shrink: 0;
    }

    .rota-card-footer .btn {
        width: 100%;
        margin-top: 8px;
    }

    .observacao-input {
        resize: none;
        height: 70px;
    }

    .rota-sem-entrega {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    @media (max-width: 900px) {

        .rota-container {
            height: calc(100vh - 60px);
        }

        #map {
            width: 100%;
            height: 55%;
        }

        .rota-card {
            top: auto;
            bottom: 10px;
            left: 10px;
            right: 10px;
            width: auto;
            height: 42%;
        }
    }
</style>


<div class="rota-container">

    @if(!$entrega)

        <div class="rota-sem-entrega">

            <div>

                <i
                    data-feather="map"
                    style="width:60px;height:60px;"
                    class="text-muted mb-3"
                ></i>

                <h4>
                    Nenhuma rota em andamento
                </h4>

                <p class="text-muted">
                    Você não possui nenhuma entrega aceita ou em trânsito.
                </p>

                <a
                    href="{{ route('entregador.dashboard') }}"
                    class="btn btn-primary"
                >
                    Voltar para entregas
                </a>
            </div>

        </div>

    @else

        {{-- MAPA --}}

        <div id="map"></div>


        {{-- CARD --}}

        <div class="rota-card">

            <div class="rota-card-header">

                <h5 class="mb-1">
                    {{ $entrega->nome_produto }}
                </h5>

                <small class="text-muted">
                    {{ $entrega->empresa->usuario->nome ?? 'Empresa' }}
                </small>

            </div>


            <div class="rota-card-body">

                {{-- ORIGEM --}}

                <div class="info-item">

                    <span class="info-label">
                        Retirada
                    </span>

                    <span class="info-value">

                        {{ $entrega->enderecoOrigem->logradouro }},
                        {{ $entrega->enderecoOrigem->numero }}

                        <br>

                        {{ $entrega->enderecoOrigem->bairro }},
                        {{ $entrega->enderecoOrigem->cidade }} -
                        {{ $entrega->enderecoOrigem->estado }}

                    </span>

                </div>


                {{-- DESTINO --}}

                <div class="info-item">

                    <span class="info-label">
                        Destino
                    </span>

                    <span class="info-value">

                        {{ $entrega->enderecoDestino->logradouro }},
                        {{ $entrega->enderecoDestino->numero }}

                        <br>

                        {{ $entrega->enderecoDestino->bairro }},
                        {{ $entrega->enderecoDestino->cidade }} -
                        {{ $entrega->enderecoDestino->estado }}

                    </span>

                </div>


                {{-- INFORMAÇÕES --}}

                <div class="row">

                    <div class="col-4">

                        <div class="info-item">

                            <span class="info-label">
                                Preço
                            </span>

                            <span class="info-value">
                                R$
                                {{ number_format($entrega->preco, 2, ',', '.') }}
                            </span>

                        </div>

                    </div>


                    <div class="col-4">

                        <div class="info-item">

                            <span class="info-label">
                                Distância
                            </span>

                            <span
                                id="distancia"
                                class="info-value"
                            >
                                ...
                            </span>

                        </div>

                    </div>


                    <div class="col-4">

                        <div class="info-item">

                            <span class="info-label">
                                Estimativa
                            </span>

                            <span
                                id="tempo"
                                class="info-value"
                            >
                                ...
                            </span>

                        </div>

                    </div>

                </div>


                {{-- DESCRIÇÃO --}}

                @if($entrega->descricao)

                    <div class="info-item">

                        <span class="info-label">
                            Descrição
                        </span>

                        <span class="info-value">
                            {{ $entrega->descricao }}
                        </span>

                    </div>

                @endif


                {{-- OBSERVAÇÃO --}}

                <div class="info-item">

                    <label
                        for="observacoes"
                        class="info-label"
                    >
                        Observação
                    </label>

                    <textarea
                        id="observacoes"
                        class="form-control observacao-input"
                        placeholder="Digite uma observação..."
                    >{{ $entrega->observacoes }}</textarea>

                </div>

            </div>


            {{-- BOTÕES --}}

            <div class="rota-card-footer">

                <form
                    method="POST"
                    action="{{ route('entrega.finalizar', $entrega->id) }}"
                >

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="btn btn-success"
                        onclick="return confirm('Deseja finalizar esta entrega?')"
                    >

                        <i
                            data-feather="check-circle"
                            class="me-1"
                        ></i>

                        Finalizar entrega

                    </button>

                </form>


                <button
                    type="button"
                    class="btn btn-outline-danger"
                    id="btnObservacao"
                >

                    <i
                        data-feather="message-square"
                        class="me-1"
                    ></i>

                    Registrar observação

                </button>

            </div>

        </div>

    @endif

</div>

@endsection


@if($entrega)

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
        .bindPopup('Retirada');


    L.marker(destino)
        .addTo(map)
        .bindPopup('Destino');


    /*
     * OSRM
     *
     * Rota pelas ruas.
     */
    const url =
        'https://router.project-osrm.org/route/v1/driving/' +
        `${origem[1]},${origem[0]};` +
        `${destino[1]},${destino[0]}` +
        '?overview=full&geometries=geojson';


    fetch(url)

        .then(response => response.json())

        .then(data => {

            if (
                !data.routes ||
                data.routes.length === 0
            ) {
                throw new Error('Rota não encontrada.');
            }


            const rota = data.routes[0];


            /*
             * DISTÂNCIA
             */

            const distancia =
                rota.distance / 1000;

            document.getElementById('distancia')
                .textContent =
                distancia
                    .toFixed(1)
                    .replace('.', ',') + ' km';


            /*
             * TEMPO
             */

            const minutos =
                Math.round(rota.duration / 60);

            let tempo;

            if (minutos < 60) {

                tempo =
                    minutos + ' min';

            } else {

                const horas =
                    Math.floor(minutos / 60);

                const min =
                    minutos % 60;

                tempo =
                    horas + 'h ' + min + 'min';

            }

            document.getElementById('tempo')
                .textContent = tempo;


            /*
             * CONVERTE A ROTA
             */

            const pontos =
                rota.geometry.coordinates.map(
                    ponto => [
                        ponto[1],
                        ponto[0]
                    ]
                );


            const linha =
                L.polyline(
                    pontos,
                    {
                        color: '#6D4AFF',
                        weight: 5,
                        opacity: .9
                    }
                ).addTo(map);


            /*
             * ENQUADRAR SOMENTE NO ESPAÇO DO MAPA
             */

            map.fitBounds(
                linha.getBounds(),
                {
                    padding: [40, 40],
                    maxZoom: 16
                }
            );

        })

        .catch(error => {

            console.error(error);

            const bounds =
                L.latLngBounds([
                    origem,
                    destino
                ]);

            map.fitBounds(
                bounds,
                {
                    padding: [40, 40],
                    maxZoom: 15
                }
            );

            document.getElementById('distancia')
                .textContent = 'Indisponível';

            document.getElementById('tempo')
                .textContent = 'Indisponível';

        });


    /*
     * REGISTRAR OBSERVAÇÃO
     */

    document
        .getElementById('btnObservacao')
        .addEventListener('click', function () {

            const observacao =
                document
                    .getElementById('observacoes')
                    .value
                    .trim();


            if (!observacao) {

                alert('Digite uma observação.');

                return;
            }


            fetch(
                '{{ route("entrega.ocorrencia", $entrega->id) }}',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                            '{{ csrf_token() }}',

                        'Accept':
                            'application/json'
                    },

                    body: JSON.stringify({
                        observacoes: observacao
                    })
                }
            )

            .then(response => {

                if (!response.ok) {
                    throw new Error();
                }

                return response.json();

            })

            .then(() => {

                alert(
                    'Observação registrada com sucesso.'
                );

            })

            .catch(() => {

                alert(
                    'Não foi possível registrar a observação.'
                );

            });

        });


    /*
     * Corrige o tamanho do mapa
     */

    setTimeout(() => {

        map.invalidateSize();

    }, 300);

});

</script>

@endpush

@endif