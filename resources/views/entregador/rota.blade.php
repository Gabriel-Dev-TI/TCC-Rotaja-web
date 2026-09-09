@extends('layouts.app')

@section('title', 'Rota da Entrega')

@section('content')

<div class="container-fluid">
@if(!$entrega)

    <div class="card">
        <div class="card-body text-center">

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
    <div class="row">

        <div class="col-lg-4 mb-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h4 class="fw-bold mb-3">
                        Rota da entrega
                    </h4>

                    <div class="mb-1">
                        <strong>Produto:</strong>
                        {{ $entrega->nome_produto }}
                    </div>
                    <div class="mb-1">
                        <strong>Preço:</strong>
                        R$ {{ ucfirst(str_replace('.', ',', $entrega->preco)) }}
                    </div>

                    <div class="mb-1">
                        <strong>Status:</strong>
                        {{ ucfirst(str_replace('_', ' ', $entrega->status)) }}
                    </div>

                    <hr>

                    <h6 class="fw-bold">
                         Retirada
                    </h6>

                    <p class="text-muted mb-3">
                        {{ $entrega->enderecoOrigem->logradouro }},
                        {{ $entrega->enderecoOrigem->numero }}
                        <br>

                        {{ $entrega->enderecoOrigem->bairro }},
                        {{ $entrega->enderecoOrigem->cidade }} -
                        {{ $entrega->enderecoOrigem->estado }}
                    </p>

                    <h6 class="fw-bold">
                         Destino
                    </h6>

                    <p class="text-muted mb-3">
                        {{ $entrega->enderecoDestino->logradouro }},
                        {{ $entrega->enderecoDestino->numero }}
                        <br>

                        {{ $entrega->enderecoDestino->bairro }},
                        {{ $entrega->enderecoDestino->cidade }} -
                        {{ $entrega->enderecoDestino->estado }}
                    </p>

                    <hr>

                    <div class="row text-center mb-3">

                        <div class="col-6">

                            <div class="small text-muted">
                                Distância
                            </div>

                            <strong id="distancia">
                                Calculando...
                            </strong>

                        </div>

                        <div class="col-6">

                            <div class="small text-muted">
                                Tempo
                            </div>

                            <strong id="tempo">
                                Calculando...
                            </strong>

                        </div>

                    </div>

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


        <div class="col-lg-8">

            <div class="card shadow-sm border-0 overflow-hidden" style="height: 640px;">

                <div id="map" style="width: 100%; height: 100%;"></div>

            </div>

        </div>

    </div>
    @endif

</div>

@if($entrega)
<link href="https://api.mapbox.com/mapbox-gl-js/v3.30.0/mapbox-gl.css"rel="stylesheet"/>
<script src="https://api.mapbox.com/mapbox-gl-js/v3.30.0/mapbox-gl.js"></script>





<script>

document.addEventListener('DOMContentLoaded', function () {

    mapboxgl.accessToken = @json(config('services.mapbox.public_token'));

    const origem = [
        {{ $entrega->enderecoOrigem->longitude }},
        {{ $entrega->enderecoOrigem->latitude }}
    ];

    const destino = [
        {{ $entrega->enderecoDestino->longitude }},
        {{ $entrega->enderecoDestino->latitude }}
    ];

    let map = null;
    let marcadorEntregador = null;
    let marcadorOrigem = null;
    let marcadorDestino = null;
    let primeiraLocalizacao = true;
    let ultimaPosicaoRota = null;
    let distanciaEntregadorOrigem = 0;
    let distanciaOrigemDestino = 0;
    let tempoEntregadorOrigem = 0;
    let tempoOrigemDestino = 0;


    function criarIcone(html, cor) {

        const elemento = document.createElement('div');

        elemento.style.width = '44px';
        elemento.style.height = '44px';

        elemento.style.display = 'flex';
        elemento.style.alignItems = 'center';
        elemento.style.justifyContent = 'center';

        elemento.style.background = cor;

        elemento.style.borderRadius = '50%';

        elemento.style.border = '3px solid white';

        elemento.style.boxShadow =
            '0 3px 12px rgba(0,0,0,0.25)';

        elemento.innerHTML = html;

        return elemento;
    }

    const iconeEntregador = criarIcone(
        `
        <svg
            width="25"
            height="25"
            viewBox="0 0 24 24"
            fill="white"
        >
            <path d="
                M3 6
                C3 4.9 3.9 4 5 4
                H14
                C15.1 4 16 4.9 16 6
                V8
                H19
                C19.7 8 20.3 8.4 20.7 9
                L22 12
                V17
                H20
                C20 18.7 18.7 20 17 20
                C15.3 20 14 18.7 14 17
                H10
                C10 18.7 8.7 20 7 20
                C5.3 20 4 18.7 4 17
                H2
                V7
                C2 6.4 2.4 6 3 6
                Z

                M16 10
                V14
                H20
                L18.5 10
                Z

                M7 15
                C5.9 15 5 15.9 5 17
                C5 18.1 5.9 19 7 19
                C8.1 19 9 18.1 9 17
                C9 15.9 8.1 15 7 15
                Z

                M17 15
                C15.9 15 15 15.9 15 17
                C15 18.1 15.9 19 17 19
                C18.1 19 19 18.1 19 17
                C19 15.9 18.1 15 17 15
                Z
            "/>
        </svg>
        `,
        '#6D4AFF'
    );

    const iconeOrigem = criarIcone(
        `
        <svg
            width="25"
            height="25"
            viewBox="0 0 24 24"
            fill="white"
        >
            <path d="
                M21 8
                L12 3
                L3 8
                L12 13
                Z
            "/>

            <path d="
                M5 10
                V17
                L12 21
                L19 17
                V10
                L12 14
                Z
            "/>
        </svg>
        `,
        '#8C6CFF'
    );

    const iconeDestino = criarIcone(
        `
        <svg
            width="25"
            height="25"
            viewBox="0 0 24 24"
            fill="white"
        >
            <path d="
                M3 11
                L12 3
                L21 11
                V21
                H3
                Z
            "/>

            <rect
                x="9"
                y="14"
                width="6"
                height="7"
                fill="#B8A5FF"
            />
        </svg>
        `,
        '#B8A5FF'
    );

    map = new mapboxgl.Map({

        container: 'map',

        style: 'mapbox://styles/mapbox/streets-v12',

        center: [
            {{ $entrega->enderecoOrigem->longitude }},
            {{ $entrega->enderecoOrigem->latitude }}
        ],

        zoom: 14,

        attributionControl: true

    });


    map.addControl(
        new mapboxgl.NavigationControl(),
        'top-right'
    );


    marcadorOrigem = new mapboxgl.Marker({
        element: iconeOrigem
    })
    .setLngLat(origem)
    .setPopup(
        new mapboxgl.Popup({
            offset: 25
        }).setHTML(`
            <strong>Local de retirada</strong>
        `)
    )
    .addTo(map);


    marcadorDestino = new mapboxgl.Marker({
        element: iconeDestino
    })
    .setLngLat(destino)
    .setPopup(
        new mapboxgl.Popup({
            offset: 25
        }).setHTML(`
            <strong>Destino da entrega</strong>
        `)
    )
    .addTo(map);


    function adicionarRota(
        id,
        geometria,
        cor,
        largura = 6
    ) {

        if (map.getLayer(id)) {

            map.removeLayer(id);

        }

        if (map.getSource(id)) {

            map.removeSource(id);

        }


        map.addSource(id, {

            type: 'geojson',

            data: {

                type: 'Feature',

                properties: {},

                geometry: geometria

            }

        });


        map.addLayer({

            id: id,

            type: 'line',

            source: id,

            layout: {

                'line-join': 'round',

                'line-cap': 'round'

            },

            paint: {

                'line-color': cor,

                'line-width': largura,

                'line-opacity': 0.9

            }

        });

    }

    async function calcularRotaOrigemDestino() {

        try {

            const url = `https://api.mapbox.com/directions/v5/mapbox/driving/` +
                        `${origem[0]},${origem[1]};` +
                        `${destino[0]},${destino[1]}` +
                        `?geometries=geojson` +
                        `&overview=full` +
                        `&steps=false` +
                        `&access_token=${mapboxgl.accessToken}`;

            const resposta = await fetch(url);

            if (!resposta.ok) {

                throw new Error(
                    `Erro HTTP ${resposta.status}`
                );

            }


            const dados = await resposta.json();


            if (
                !dados.routes ||
                !dados.routes.length
            ) {

                throw new Error(
                    'Nenhuma rota encontrada.'
                );

            }


            const rota = dados.routes[0];


            distanciaOrigemDestino =
                rota.distance;


            tempoOrigemDestino =
                rota.duration;


            adicionarRota(
                'rota-origem-destino',
                rota.geometry,
                '#B8A5FF',
                6
            );


            atualizarInformacoes();


        } catch (erro) {

            console.error(
                'Erro ao calcular rota origem → destino:',
                erro
            );

        }

    }

    async function calcularRotaEntregadorOrigem(
        longitude,
        latitude
    ) {

        try {

            const url = `https://api.mapbox.com/directions/v5/mapbox/driving/` +
                        `${longitude},${latitude};` +
                        `${origem[0]},${origem[1]}` +
                        `?geometries=geojson` +
                        `&overview=full` +
                        `&steps=false` +
                        `&access_token=${mapboxgl.accessToken}`;

            const resposta = await fetch(url);


            if (!resposta.ok) {
                throw new Error(`Erro HTTP ${resposta.status}`);
            }


            const dados = await resposta.json();


            if (
                !dados.routes ||
                !dados.routes.length
            ) {
                throw new Error('Nenhuma rota encontrada.');
            }


            const rota = dados.routes[0];


            distanciaEntregadorOrigem = rota.distance;


            tempoEntregadorOrigem = rota.duration;


            adicionarRota(
                'rota-entregador-origem',
                rota.geometry,
                '#6D4AFF',
                6
            );


            atualizarInformacoes();


        } catch (erro) {

            console.error(
                'Erro ao calcular rota entregador → origem:',
                erro
            );

        }

    }

    function atualizarInformacoes() {

        const distanciaTotal = distanciaEntregadorOrigem + distanciaOrigemDestino;
        const tempoTotal = tempoEntregadorOrigem + tempoOrigemDestino;


        if (distanciaTotal > 0) {
            const km = distanciaTotal / 1000;
            document.getElementById('distancia').innerText = km.toFixed(1) + ' km';
        }


        if (tempoTotal > 0) {

            const minutos = Math.ceil(tempoTotal / 60);

            if (minutos >= 60) {

                const horas = Math.floor(minutos / 60);
                const minutosRestantes = minutos % 60;
                document.getElementById('tempo').innerText = `${horas}h ${minutosRestantes}min`;

            } else {
                document.getElementById('tempo').innerText = `${minutos} min`;
            }

        }

    }

    if (!navigator.geolocation) {
        alert('Seu navegador não suporta geolocalização.');
        return;
    }


    navigator.geolocation.watchPosition(

        function (posicao) {

            const latitude = posicao.coords.latitude;
            const longitude =posicao.coords.longitude;

            const novaPosicao = [
                longitude,
                latitude
            ];

            if (!marcadorEntregador) {

                marcadorEntregador =
                    new mapboxgl.Marker({
                        element: iconeEntregador
                    })
                    .setLngLat(novaPosicao)
                    .setPopup(
                        new mapboxgl.Popup({
                            offset: 25
                        }).setHTML(`
                            <strong>Você está aqui</strong>
                        `)
                    )
                    .addTo(map);

                if (primeiraLocalizacao) {

                    map.flyTo({

                        center: novaPosicao,

                        zoom: 16,

                        essential: true

                    });


                    primeiraLocalizacao = false;

                }

            } else {

                marcadorEntregador.setLngLat(
                    novaPosicao
                );

            }

            if (!ultimaPosicaoRota) {

                ultimaPosicaoRota = novaPosicao;

                calcularRotaEntregadorOrigem(
                    longitude,
                    latitude
                );

                return;

            }


            const distanciaMovida =
                calcularDistanciaMetros(
                    ultimaPosicaoRota[1],
                    ultimaPosicaoRota[0],
                    latitude,
                    longitude
                );

            // Só recalcula depois de aproximadamente 50 metros.

            if (distanciaMovida >= 50) {

                ultimaPosicaoRota = novaPosicao;

                calcularRotaEntregadorOrigem(
                    longitude,
                    latitude
                );

            }

        },

        function (erro) {

            console.error( 'Erro ao obter localização:', erro );


            switch (erro.code) {

                case 1:

                    alert(
                        'Permissão de localização negada. ' +
                        'Permita o acesso à localização no navegador.'
                    );

                    break;


                case 2:

                    alert(
                        'Não foi possível obter sua localização.'
                    );

                    break;


                case 3:

                    alert(
                        'Tempo limite para obter sua localização.'
                    );

                    break;

            }

        },

        {

            enableHighAccuracy: true,

            timeout: 10000,

            maximumAge: 0

        }

    );

    function calcularDistanciaMetros(
        lat1,
        lon1,
        lat2,
        lon2
    ) {

        const R = 6371000;


        const dLat =
            (lat2 - lat1) *
            Math.PI / 180;


        const dLon =
            (lon2 - lon1) *
            Math.PI / 180;


        const a =
            Math.sin(dLat / 2) *
            Math.sin(dLat / 2) +

            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *

            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);


        const c =
            2 *
            Math.atan2(
                Math.sqrt(a),
                Math.sqrt(1 - a)
            );


        return R * c;

    }

    map.on('load', function () {

        calcularRotaOrigemDestino();

    });

    document
        .getElementById('formFinalizar')
        .addEventListener('submit', function (event) {

            const confirmar =
                confirm(
                    'Deseja realmente finalizar esta entrega?'
                );


            if (!confirmar) {

                event.preventDefault();

            }

        });

    document
        .getElementById('formCancelar')
        .addEventListener('submit', function (event) {

            const confirmar =
                confirm(
                    'Deseja realmente cancelar esta entrega?'
                );


            if (!confirmar) {

                event.preventDefault();

            }

        });

});

</script>
  @endif

@endsection

