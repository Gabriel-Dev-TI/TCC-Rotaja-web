<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rota Já</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <meta name="description"
        content="RotaJá é uma plataforma para gerenciamento e realização de entregas, conectando empresas e entregadores.">
    <meta name="author" content="RotaJá">

    <link
        href="{{ asset('css/app.css') }}"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet"
    >

</head>

<body>

<div class="wrapper">

    <div class="main d-flex flex-column min-vh-100">

        <nav class="navbar navbar-expand navbar-light navbar-bg px-3 fixed-top">
			<a href="/">
				<img
            src="{{ asset('img/logo.png') }}"
            alt="RotaJá"
            class="img-fluid"
            style="width: 150px; height: auto;"
        >
                </a>

</nav>


<main class="flex-grow-1 d-flex w-100 align-items-center pt-5">

    <div class="container">

        @yield('content')

    </div>

</main>


@include('layouts.footer')

    </div>

</div>


<script src="{{ asset('js/app.js') }}"></script>

</body>

</html>