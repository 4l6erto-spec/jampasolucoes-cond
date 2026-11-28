<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123c35">
    <title>{{ config('app.name') }}</title>
    <style>
        :root { font-family: Inter, system-ui, -apple-system, sans-serif; color: #172321; background: #edf3f1; }
        * { box-sizing: border-box; }
        body { margin: 0; }
        .topbar { background: #123c35; color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
        .topbar a { color: #fff; margin-right: 12px; text-decoration: none; }
        .topbar form { display: inline; }
        .topbar button { color: #fff; background: transparent; border: 1px solid #ffffff77; padding: 7px 10px; border-radius: 6px; }
        .wrap { max-width: 1100px; margin: auto; padding: 28px 20px; }
        .hero, .card { background: #fff; border: 1px solid #dde7e3; border-radius: 14px; padding: 22px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; margin-top: 18px; }
        .metric { font-size: 30px; font-weight: 700; color: #17694f; }
        .button { display: inline-block; color: #fff; background: #17694f; padding: 10px 14px; border-radius: 7px; text-decoration: none; }
        .flash { background: #dcf5eb; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        @media (max-width: 700px) { .topbar { align-items: flex-start; flex-direction: column; } .wrap { padding: 18px 12px; } }
    </style>
</head>
<body>
<header class="topbar">
    <nav>
        <strong>{{ config('app.name') }}</strong>
        <a href="{{ route('dashboard') }}">Início</a>
        <a href="{{ route('visitors.index') }}">Visitantes</a>
        <a href="{{ route('reservations.index') }}">Reservas</a>
        <a href="{{ route('announcements.index') }}">Comunicados</a>
        <a href="{{ route('tickets.index') }}">Chamados</a>
        <a href="{{ route('deliveries.index') }}">Encomendas</a>
        @if (auth()->user()->isAdmin() || auth()->user()->isPortaria())
            <a href="{{ route('accesses.index') }}">Acessos</a>
        @endif
    </nav>
    <div>
        {{ auth()->user()->name }} · {{ auth()->user()->role }}
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sair</button>
        </form>
    </div>
</header>
<main class="wrap">
    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash">{{ $errors->first() }}</div>
    @endif

    <section class="hero">
        <h1>Visão geral</h1>
        <p>Bem-vindo, {{ auth()->user()->name }}. Acompanhe as operações do condomínio.</p>
    </section>

    <section class="grid" aria-label="Indicadores">
        <article class="card"><div class="metric">{{ $visitorCount }}</div>Visitantes ainda no condomínio</article>
        <article class="card"><div class="metric">{{ $deliveryCount }}</div>Encomendas aguardando retirada</article>
        <article class="card"><div class="metric">{{ $reservationCount }}</div>Reservas a partir de hoje</article>
        <article class="card"><div class="metric">{{ $ticketCount }}</div>Chamados em aberto</article>
    </section>

    <section class="grid" aria-label="Módulos operacionais">
        <article class="card"><h2>Visitantes</h2><p>Autorize visitantes e acompanhe entrada e saída.</p><a class="button" href="{{ route('visitors.index') }}">Abrir módulo</a></article>
        <article class="card"><h2>Reservas</h2><p>Solicite espaços e acompanhe aprovações.</p><a class="button" href="{{ route('reservations.index') }}">Abrir módulo</a></article>
        <article class="card"><h2>Comunicados</h2><p>Leia informações e avisos do condomínio.</p><a class="button" href="{{ route('announcements.index') }}">Abrir módulo</a></article>
        <article class="card"><h2>Chamados</h2><p>Registre solicitações e acompanhe o atendimento.</p><a class="button" href="{{ route('tickets.index') }}">Abrir módulo</a></article>
        <article class="card"><h2>Encomendas</h2><p>Acompanhe recebimentos e retiradas.</p><a class="button" href="{{ route('deliveries.index') }}">Abrir módulo</a></article>
    </section>
</main>
</body>
</html>
