<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RotaJá - Logística Urbana</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --primary-purple: #7B33F4;
            --primary-purple-dark: #6221E2;
            --primary-blue: #0012FF;
            --primary-blue-dark: #000EC5;
            --black-text: #090D16;
            --muted-text: #475569;
            --bg-light: #F8FAFC;
            --border-color: #E2E8F0;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--black-text);
            background-color: var(--bg-light);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Appbar */
        .appbar {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 0;
        }

        .appbar .logo {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -1px;
            color: var(--black-text);
            text-decoration: none;
        }

        .appbar .logo span { color: var(--primary-purple); }

        .appbar .nav-link {
            color: var(--muted-text) !important;
            font-weight: 600;
            font-size: 15px;
            padding: 8px 16px !important;
            transition: color 0.2s;
        }

        .appbar .nav-link:hover, .appbar .nav-link.active {
            color: var(--primary-purple) !important;
        }

        /* Buttons */
        .btn-custom {
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 24px;
            transition: all 0.2s ease;
        }

        .btn-purple {
            background-color: var(--primary-purple);
            color: #FFFFFF;
            border: none;
        }

        .btn-purple:hover {
            background-color: var(--primary-purple-dark);
            color: #FFFFFF;
        }

        .btn-blue {
            background-color: var(--primary-blue);
            color: #FFFFFF;
            border: none;
        }

        .btn-blue:hover {
            background-color: var(--primary-blue-dark);
            color: #FFFFFF;
        }

        .btn-dark-cta {
            background-color: var(--black-text);
            color: #FFFFFF;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 20px;
            text-decoration: none;
        }

        .btn-dark-cta:hover { color: #FFFFFF; opacity: 0.9; }

        /* Cards e Containers */
        .main-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.02);
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            font-size: 15px;
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 4px rgba(123, 51, 244, 0.1);
        }

        .badge-status {
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
        }

        .badge-pendente { background: #FEF3C7; color: #92400E; }
        .badge-andamento { background: #DBEAFE; color: #1E40AF; }
        .badge-concluida { background: #D1FAE5; color: #065F46; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg appbar sticky-top">
        <div class="container">
            <a class="logo" href="{{ url('/') }}">Rota<span>Já</span></a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <i data-lucide="menu"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-3 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('empresas.create') }}">Para Empresas</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('entregadores.create') }}">Para Entregadores</a></li>
                    @auth
                        @if(auth()->user()->empresa_id)
                            <li class="nav-item"><a class="nav-link" href="{{ route('empresa.dashboard') }}">Painel Empresa</a></li>
                        @elseif(auth()->user()->entregador_id)
                            <li class="nav-item"><a class="nav-link" href="{{ route('entregador.dashboard') }}">Painel Entregador</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link" href="{{ route('entregas.historico') }}">Histórico</a></li>
                    @endauth
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn fw-semibold text-dark">Entrar</a>
                        <a href="{{ route('empresas.create') }}" class="btn-dark-cta">Começar Agora</a>
                    @else
                        <a href="{{ route('perfil.configuracoes') }}" class="btn btn-light border rounded-3 p-2 me-1" title="Minha Conta">
                            <i data-lucide="user" style="width: 18px;"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 fw-bold px-3 py-2">Sair</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4">
        @yield('content')
    </main>

    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-muted fs-7 fw-medium">
            &copy; {{ date('Y') }} RotaJá Logística Urbana. Todos os direitos reservados.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script> lucide.createIcons(); </script>
</body>
</html>