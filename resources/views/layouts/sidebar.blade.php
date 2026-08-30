<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
                <div class="text-center w-100">
    			<h1 class="m-0 mt-3 fw-bold">
      		  <a href="{{ url('/') }}" class="navbar-brand">
                <img
                    src="{{ asset('img/logo.png') }}"
                    alt="RotaJá"
                    class="img-fluid"
                    style="width: 150px; height: auto; filter: brightness(0) invert(1);"
                >
            </a>
  			  </h1>
			</div>

				<ul class="sidebar-nav">

    <li class="sidebar-header">
        Páginas
    </li>

    @if (auth()->user()->cargo === 'admin')

        {{-- PAINEL --}}
        <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('admin.dashboard') }}"
            >
                <i
                    class="align-middle"
                    data-feather="sliders"
                ></i>

                <span class="align-middle">
                    Painel
                </span>
            </a>

        </li>


        <li class="sidebar-item {{ request()->routeIs('entregadores.*') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('admin.entregadores.index') }}"
            >
                <i
                    class="align-middle"
                    data-feather="truck"
                ></i>

                <span class="align-middle">
                    Entregadores
                </span>
            </a>

        </li>

        <li class="sidebar-item {{ request()->routeIs('empresas.*') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('admin.empresas.index') }}"
            >
                <i
                    class="align-middle"
                    data-feather="users"
                ></i>

                <span class="align-middle">
                    Empresas
                </span>
            </a>

        </li>

        <li class="sidebar-item {{ request()->routeIs('entregas.*') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('admin.entregas.index') }}"
            >
                <i
                    class="align-middle"
                    data-feather="shopping-cart"
                ></i>

                <span class="align-middle">
                    Entregas
                </span>
            </a>

        </li>

    @elseif (auth()->user()->cargo === 'entregador')

        {{-- PAINEL --}}
        <li class="sidebar-item {{ request()->routeIs('entregador.dashboard') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('entregador.dashboard') }}"
            >
                <i
                    class="align-middle"
                    data-feather="sliders"
                ></i>

                <span class="align-middle">
                    Painel
                </span>
            </a>

        </li>

        <li class="sidebar-item {{ request()->routeIs('rota') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('rota') }}"
            >
                <i
                    class="align-middle"
                    data-feather="map"
                ></i>

                <span class="align-middle">
                    Rota
                </span>
            </a>

        </li>

    @elseif (auth()->user()->cargo === 'empresa')

        {{-- PAINEL --}}
        <li class="sidebar-item {{ request()->routeIs('empresa.dashboard') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('empresa.dashboard') }}"
            >
                <i
                    class="align-middle"
                    data-feather="sliders"
                ></i>

                <span class="align-middle">
                    Painel
                </span>
            </a>

        </li>

        <li class="sidebar-item {{ request()->routeIs('entregas.create') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('entregas.create') }}"
            >
                <i
                    class="align-middle"
                    data-feather="shopping-cart"
                ></i>

                <span class="align-middle">
                    Cadastrar entrega
                </span>
            </a>

        </li>

        <li class="sidebar-item {{ request()->routeIs('enderecos.*') ? 'active' : '' }}">

            <a
                class="sidebar-link"
                href="{{ route('enderecos.index') }}"
            >
                <i
                    class="align-middle"
                    data-feather="map-pin"
                ></i>

                <span class="align-middle">
                    Endereços
                </span>
            </a>

        </li>

    @endif


    @if (auth()->user()->cargo != 'admin')

    <li class="sidebar-item {{ request()->routeIs('historico')? 'active' : '' }}">

        <a
            class="sidebar-link"
            href="{{ route('historico') }}"
        >
            <i
                class="align-middle"
                data-feather="clock"
            ></i>

            <span class="align-middle">
                Histórico
            </span>
        </a>

    </li>
    @endif

    <li class="sidebar-item {{ request()->routeIs('perfil.show') || request()->routeIs('perfil.edit')  ? 'active' : '' }}">

        <a
            class="sidebar-link"
            href="{{ route('perfil.show') }}"
        >
            <i
                class="align-middle"
                data-feather="user"
            ></i>

            <span class="align-middle">
                Perfil
            </span>
        </a>

    </li>

    <li class="sidebar-item">

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="border-0 bg-transparent sidebar-link"
            >

                <i
                    class="align-middle"
                    data-feather="log-out"
                ></i>

                <span class="align-middle">
                    Sair
                </span>

            </button>

        </form>

    </li>

</ul>
			</div>
		</nav>