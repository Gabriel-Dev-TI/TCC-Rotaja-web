<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	 <meta name="description"content="RotaJá é uma plataforma para gerenciamento e realização de entregas, conectando empresas e entregadores.">
    <meta name="author" content="RotaJá">

	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web,laravel,rotaja,delivery,entregas,entregador">

	<link rel="preconnect" href="https://fonts.gstatic.com">

	<title>@yield('title', 'Rota Já')</title>

	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
		@include('layouts.sidebar')

		<div class="main">

            @include('layouts.navbar')
	    
            <main class="content">
				<div class="container-fluid p-0">
                	@yield('content')
				</div>
            </main>

            @include('layouts.footer')

		</div>
	</div>

	<script src="{{ asset('js/app.js') }}"></script>

	@stack('scripts')

</body>

</html>