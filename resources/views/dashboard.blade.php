<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123c35">
    <title>{{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light; font-family: Inter, system-ui, sans-serif; background: #f3f6f5; color: #172321; }
        * { box-sizing: border-box; }
        body { margin: 0; }
        header { padding: 1.2rem max(1.25rem, calc((100% - 1100px)/2)); background: #123c35; color: white; }
        main { max-width: 1100px; margin: 2rem auto; padding: 0 1.25rem; }
        .welcome { padding: 2rem; background: white; border-radius: 1rem; box-shadow: 0 8px 30px #173a3010; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .card { padding: 1.25rem; background: white; border: 1px solid #e1e9e6; border-radius: .8rem; }
        h1 { margin-top: 0; } h2 { font-size: 1.05rem; } p { line-height: 1.55; color: #52615d; }
    </style>
</head>
<body>
<header><strong>{{ config('app.name') }}</strong></header>
<main>
    <section class="welcome">
        <h1>Gestão do condomínio, em um só lugar.</h1>
        <p>A base do sistema está instalada. Os módulos serão habilitados gradualmente, com acesso por perfil e foco na privacidade dos moradores.</p>
    </section>
    <section class="grid" aria-label="Módulos do sistema">
        @foreach (['Acessos', 'Visitantes e QR Code', 'Encomendas', 'Reservas', 'Comunicados', 'Chamados', 'Financeiro', 'Cadastros'] as $module)
            <article class="card"><h2>{{ $module }}</h2><p>Módulo planejado para as próximas etapas.</p></article>
        @endforeach
    </section>
</main>
</body>
</html>
