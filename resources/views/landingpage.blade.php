<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description"
      content="RotaJá é uma plataforma para gerenciamento e realização de entregas, conectando empresas e entregadores.">
    <meta name="author" content="RotaJá">
    <meta name="google-site-verification" content="BzloqsDMB3hIWTsVLMNnJwMUgD3TcJ_yneR5k8f7eno" />
    <link rel="canonical" href="https://rotaja.wasmer.app/">

    <title>RotaJá — Gestão de Entregas e Rotas Inteligentes</title>

    <meta property="og:type" content="website">
    <meta property="og:title" content="RotaJá — Gestão de Entregas e Rotas Inteligentes">
    <meta property="og:description"
          content="Gerencie entregas, conecte empresas e entregadores e acompanhe suas rotas com o RotaJá.">
    <meta property="og:url" content="https://rotaja.wasmer.app/">
    <meta property="og:image" content="{{ asset('img/logo.webp') }}">
    <meta property="og:locale" content="pt_BR">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css"
        rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,600;1,600&display=swap"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300;0,500;0,600;0,700;1,300;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,400;1,400&display=swap"
        rel="stylesheet">

    <!-- New Age / Bootstrap CSS -->
    <link href="{{ asset('new-age-template/css/styles.css') }}" rel="stylesheet">

</head>

<body id="page-top">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNav">

        <div class="container px-5">

            <a href="{{ url('/') }}" class="navbar-brand">
                <img
                    src="{{ asset('img/logo.webp') }}"
                    alt="RotaJá"
                    class="img-fluid"
                    style="width: 150px; height: auto;"
                >
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarResponsive"
                aria-controls="navbarResponsive"
                aria-expanded="false"
                aria-label="Abrir menu"
            >
                Menu
                <i class="bi-list"></i>
            </button>


            <div class="collapse navbar-collapse" id="navbarResponsive">


                <ul class="navbar-nav ms-auto me-4 my-3 my-lg-0">

                    <li class="nav-item">
                        <a class="nav-link me-lg-3" href="#solucao">
                            Como Funciona?
                        </a>
                    </li>

                    <li class="nav-item">
                         
                        <a class="nav-link me-lg-3" href="{{ route('registro', ['tipo' => 'empresa']) }}">
                            Empresas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link me-lg-3" href="{{ route('registro', ['tipo' => 'entregador']) }}">
                            Entregadores
                        </a>
                    </li>

                </ul>
                

                <a
                    href="{{ url('/login') }}"
                    class="btn btn-primary rounded-pill px-4 mb-2 mb-lg-0"
                >
                    <span class="d-flex align-items-center">
                        <span class="small">Entrar</span>
                    </span>
                </a>
                

            </div>

        </div>

    </nav>

    <header class="masthead">

        <div class="container px-5">

            <div class="row gx-5 align-items-center">

                <div class="col-lg-6">

                    <div class="mb-5 mb-lg-0 text-center text-lg-start">

                        <h1 class="display-1 lh-1 mb-3">
                            Sua entrega na <span class="text-primary">rota</span> certa.
                        </h1>

                        <p class="lead fw-normal text-muted mb-5">
                            O RotaJá conecta empresas e entregadores para tornar
                            o gerenciamento e a realização de entregas mais
                            simples, organizados e eficiente.
                        </p>

                        <div class="d-flex flex-column flex-lg-row align-items-center">

                            <a
                                href="#"
                                class="btn btn-primary rounded-pill px-4 mb-3 mb-lg-0 me-lg-3"
                            >
                                Baixe o App
                            </a>

                            <a
                                href="#solucao"
                                class="btn btn-outline-dark rounded-pill px-4"
                            >
                                Conheça o RotaJá
                            </a>

                        </div>

                    </div>

                </div>


                <div class="col-lg-6">

                    <div class="masthead-device-mockup">

                        <svg
                            class="circle"
                            viewBox="0 0 100 100"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <defs>

                                <linearGradient
                                    id="circleGradient"
                                    gradientTransform="rotate(45)"
                                >
                                    <stop
                                        class="gradient-start-color"
                                        offset="0%"
                                    ></stop>

                                    <stop
                                        class="gradient-end-color"
                                        offset="100%"
                                    ></stop>

                                </linearGradient>

                            </defs>

                            <circle
                                cx="50"
                                cy="50"
                                r="50"
                            ></circle>

                        </svg>


                        <svg
                            class="shape-1 d-none d-sm-block"
                            viewBox="0 0 240.83 240.83"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <rect
                                x="-32.54"
                                y="78.39"
                                width="305.92"
                                height="84.05"
                                rx="42.03"
                                transform="translate(120.42 -49.88) rotate(45)"
                            ></rect>

                            <rect
                                x="-32.54"
                                y="78.39"
                                width="305.92"
                                height="84.05"
                                rx="42.03"
                                transform="translate(-49.88 120.42) rotate(-45)"
                            ></rect>

                        </svg>


                        <svg
                            class="shape-2 d-none d-sm-block"
                            viewBox="0 0 100 100"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <circle
                                cx="50"
                                cy="50"
                                r="50"
                            ></circle>

                        </svg>


                        <div class="device-wrapper">

                            <div
                                class="device"
                                data-device="iPhoneX"
                                data-orientation="portrait"
                                data-color="black"
                            >

                                <div class="screen bg-white ">

                                    <img
                                        src="{{ asset('new-age-template/assets/img/celular.webp') }}"
                                        class="img-fluid"
                                        alt="Aplicativo RotaJá"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                    >

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </header>


    <aside class="text-center bg-gradient-primary-to-secondary">

        <div class="container px-5">

            <div class="row gx-5 justify-content-center">

                <div class="col-xl-8">

                    <div class="h2 fs-1 text-white mb-0">

                        Entregas mais organizadas,
                        rotas mais eficientes e conexão
                        entre empresas e entregadores.

                    </div>

                </div>

            </div>

        </div>

    </aside>


    <section id="solucao">

        <div class="container px-5">

            <div class="row gx-5 align-items-center">

                <div class="col-lg-8 order-lg-1 mb-5 mb-lg-0">

                    <div class="container-fluid px-5">

                        <div class="row gx-5">

                            <!-- Feature 1 -->
                            <div class="col-md-6 mb-5">

                                <div class="text-center">

                                    <i class="bi-box-seam icon-feature text-gradient d-block mb-3"></i>

                                    <h3 class="font-alt">
                                        Criação de entregas
                                    </h3>

                                    <p class="text-muted mb-0">
                                        Cadastre produtos, endereços, peso,
                                        dimensões e demais informações
                                        necessárias para a entrega.
                                    </p>

                                </div>

                            </div>


                            <!-- Feature 2 -->
                            <div class="col-md-6 mb-5">

                                <div class="text-center">

                                    <i class="bi-person-check icon-feature text-gradient d-block mb-3"></i>

                                    <h3 class="font-alt">
                                        Conexão com entregadores
                                    </h3>

                                    <p class="text-muted mb-0">
                                        Entregadores podem visualizar
                                        oportunidades disponíveis e aceitar
                                        entregas pela plataforma.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="row">

                            <!-- Feature 3 -->
                            <div class="col-md-6 mb-5 mb-md-0">

                                <div class="text-center">

                                    <i class="bi-geo-alt icon-feature text-gradient d-block mb-3"></i>

                                    <h3 class="font-alt">
                                        Rotas
                                    </h3>

                                    <p class="text-muted mb-0">
                                        Visualize os endereços envolvidos na
                                        entrega e facilite o deslocamento
                                        até o destino.
                                    </p>

                                </div>

                            </div>


                            <!-- Feature 4 -->
                            <div class="col-md-6">

                                <div class="text-center">

                                    <i class="bi-bar-chart icon-feature text-gradient d-block mb-3"></i>

                                    <h3 class="font-alt">
                                        Acompanhamento
                                    </h3>

                                    <p class="text-muted mb-0">
                                        Acompanhe o status das entregas desde
                                        o cadastro até a conclusão do serviço.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Mockup lateral -->
                <div class="col-lg-4 order-lg-0">

                    <div class="features-device-mockup">

                        <svg
                            class="circle"
                            viewBox="0 0 100 100"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <defs>

                                <linearGradient
                                    id="circleGradientFeatures"
                                    gradientTransform="rotate(45)"
                                >

                                    <stop
                                        class="gradient-start-color"
                                        offset="0%"
                                    ></stop>

                                    <stop
                                        class="gradient-end-color"
                                        offset="100%"
                                    ></stop>

                                </linearGradient>

                            </defs>

                            <circle
                                cx="50"
                                cy="50"
                                r="50"
                            ></circle>

                        </svg>


                        <svg
                            class="shape-1 d-none d-sm-block"
                            viewBox="0 0 240.83 240.83"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <rect
                                x="-32.54"
                                y="78.39"
                                width="305.92"
                                height="84.05"
                                rx="42.03"
                                transform="translate(120.42 -49.88) rotate(45)"
                            ></rect>

                            <rect
                                x="-32.54"
                                y="78.39"
                                width="305.92"
                                height="84.05"
                                rx="42.03"
                                transform="translate(-49.88 120.42) rotate(-45)"
                            ></rect>

                        </svg>


                        <svg
                            class="shape-2 d-none d-sm-block"
                            viewBox="0 0 100 100"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <circle
                                cx="50"
                                cy="50"
                                r="50"
                            ></circle>

                        </svg>


                        <div class="device-wrapper">

                            <div
                                class="device"
                                data-device="iPhoneX"
                                data-orientation="portrait"
                                data-color="black"
                            >

                                 <div class="screen bg-white ">

                                    <img
                                        src="{{ asset('new-age-template/assets/img/celularMapa.webp') }}"
                                        class="img-fluid"
                                        alt="Aplicativo RotaJá"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                    >

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="bg-light" id="empresas">

        <div class="container px-5">

            <div class="row gx-5 align-items-center">

                <div class="col-lg-6 mb-5 mb-lg-0">

                    <div class="p-4 p-lg-5">

                        <div class="text-center text-lg-start">

                            <i class="bi-building text-gradient d-block mb-3"
                               style="font-size: 4rem;"></i>

                            <h2 class="font-alt mb-3">
                                Para empresas
                            </h2>

                            <p class="text-muted mb-4">
                                Tenha mais controle sobre suas entregas e
                                centralize as informações dos pedidos em um
                                único lugar.
                            </p>

                            <ul class="list-unstyled text-muted">

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Cadastre novas entregas
                                </li>

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Gerencie endereços
                                </li>

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Acompanhe o status das entregas
                                </li>

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Consulte o histórico de entregas
                                </li>

                                <li>
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Tenha mais organização no processo
                                </li>

                            </ul>

                        </div>

                    </div>

                </div>


                <div class="col-lg-6" id="entregadores">

                    <div class="p-4 p-lg-5">

                        <div class="text-center text-lg-start">

                            <i class="bi-bicycle text-gradient d-block mb-3"
                               style="font-size: 4rem;"></i>

                            <h2 class="font-alt mb-3">
                                Para entregadores
                            </h2>

                            <p class="text-muted mb-4">
                                Encontre oportunidades de entrega e acompanhe
                                seus serviços de forma simples e organizada.
                            </p>

                            <ul class="list-unstyled text-muted">

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Visualize entregas disponíveis
                                </li>

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Aceite novas entregas
                                </li>

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Consulte origem e destino
                                </li>

                                <li class="mb-3">
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Acompanhe entregas em andamento
                                </li>

                                <li>
                                    <i class="bi-check-circle-fill text-gradient me-2"></i>
                                    Consulte seu histórico
                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section>

        <div class="container px-5">

            <div class="row gx-5 align-items-center">

                <div class="col-lg-6 mb-5 mb-lg-0">

                    <h2 class="display-5 lh-1 mb-4">
                        Do cadastro à entrega concluída.
                    </h2>

                    <p class="lead fw-normal text-muted mb-5">
                        O RotaJá organiza as principais etapas do processo
                        de entrega em um único ambiente.
                    </p>


                    <div class="d-flex mb-4">

                        <div class="me-3">

                            <span class="badge rounded-pill bg-primary p-3">
                                1
                            </span>

                        </div>

                        <div>

                            <h3 class="h5 mb-1">
                                Empresa cadastra a entrega
                            </h3>

                            <p class="text-muted mb-0">
                                As informações do produto, origem e destino
                                são registradas na plataforma.
                            </p>

                        </div>

                    </div>


                    <div class="d-flex mb-4">

                        <div class="me-3">

                            <span class="badge rounded-pill bg-primary p-3">
                                2
                            </span>

                        </div>

                        <div>

                            <h3 class="h5 mb-1">
                                Entregador aceita o serviço
                            </h3>

                            <p class="text-muted mb-0">
                                O entregador visualiza as oportunidades
                                disponíveis e pode aceitar uma entrega.
                            </p>

                        </div>

                    </div>


                    <div class="d-flex">

                        <div class="me-3">

                            <span class="badge rounded-pill bg-primary p-3">
                                3
                            </span>

                        </div>

                        <div>

                            <h3 class="h5 mb-1">
                                Entrega é realizada
                            </h3>

                            <p class="text-muted mb-0">
                                O serviço avança pelos seus status até
                                chegar à conclusão.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-lg-6">

                    <div class="px-4 px-lg-5">

                        <div class="text-center">

                            <i
                                class="bi-geo-alt-fill text-gradient"
                                style="font-size: 100px;"
                            ></i>

                            <h3 class="font-alt mt-4">
                                RotaJá
                            </h3>

                            <p class="text-muted">
                                Conectando quem precisa entregar
                                com quem pode realizar a entrega.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="bg-gradient-primary-to-secondary" id="download">

        <div class="container px-5">

            <div class="text-center text-white">

                <h2 class="display-5 font-alt mb-4">
                    Pronto para colocar sua entrega na rota certa?
                </h2>

                <p class="lead mb-4">
                    Acesse o RotaJá e faça parte da plataforma.
                </p>

                <a
                    href="{{ url('/login') }}"
                    class="btn btn-light btn-lg rounded-pill px-5"
                >
                    Acessar plataforma
                </a>

            </div>

        </div>

    </section>


    <footer class="bg-black text-center py-5">

        <div class="container px-5">

            <div class="mb-4">

                <img
                    src="{{ asset('img/logo.webp') }}"
                    alt="RotaJá"
                    style="width: 130px; filter: brightness(0) invert(1);"
                >

            </div>

            <div class="text-white-50 small">

                <div class="mb-3">
                    &copy; {{ date('Y') }} RotaJá.
                    Todos os direitos reservados.
                </div>

            </div>

        </div>

    </footer>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- New Age JS -->
    <script src="{{ asset('new-age-template/js/scripts.js') }}"></script>

</body>

</html>
