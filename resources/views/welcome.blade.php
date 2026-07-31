@extends('layouts.app')

@section('content')
<style>
    .hero-top-block { padding: 70px 0 50px 0; text-align: center; }
    .hero-title { font-size: 3.5rem; font-weight: 900; letter-spacing: -2px; line-height: 1.15; max-width: 850px; margin: 0 auto; }
    .hero-title span { color: var(--primary-purple); }
    .block-card { background-color: #FFFFFF; border: 1px solid var(--border-color); border-radius: 24px; padding: 40px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s; }
    .block-card:hover { transform: translateY(-4px); }
    .block-title { font-size: 1.8rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 12px; }
    .block-desc { font-size: 1.05rem; color: var(--muted-text); line-height: 1.6; margin-bottom: 30px; }
</style>

<header class="hero-top-block">
    <div class="container">
        <h1 class="hero-title">A plataforma que simplifica a logística urbana e conecta você ao seu <span>destino.</span></h1>
    </div>
</header>

<section class="pb-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="block-card">
                    <div>
                        <div class="mb-4"><i data-lucide="package-plus" style="width: 38px; height: 38px; color: var(--primary-purple);"></i></div>
                        <h2 class="block-title">Envie produtos e gerencie frotas corporativas</h2>
                        <p class="block-desc">Aumente a eficiência da sua operação logística. Soluções completas para e-commerce e comércio local com rastreamento contínuo e controle centralizado.</p>
                    </div>
                    <a href="{{ route('empresas.create') }}" class="btn btn-custom btn-purple w-fit d-inline-flex align-items-center gap-2">
                        Começar como empresa <i data-lucide="arrow-right" style="width: 18px;"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="block-card">
                    <div>
                        <div class="mb-4"><i data-lucide="navigation-2" style="width: 38px; height: 38px; color: var(--primary-blue);"></i></div>
                        <h2 class="block-title">Gere receita pilotando com autonomia</h2>
                        <p class="block-desc">Acesse pedidos de entrega na sua região, trabalhe nos horários que desejar e garanta repasses rápidos e transparentes direto na sua conta.</p>
                    </div>
                    <a href="{{ route('entregadores.create') }}" class="btn btn-custom btn-blue w-fit d-inline-flex align-items-center gap-2">
                        Cadastrar como parceiro <i data-lucide="arrow-right" style="width: 18px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection