<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#123c35">
    <title>{{ config('app.name') }}</title>
    <style>
        :root{font-family:Inter,system-ui,-apple-system,sans-serif;color:#172321;background:#edf3f1}
        *{box-sizing:border-box}body{margin:0}.topbar{background:#123c35;color:#fff;padding:18px 24px}.topbar strong{font-size:20px}.wrap{max-width:1100px;margin:auto;padding:32px 20px}.hero{background:#fff;border-radius:16px;padding:32px;box-shadow:0 8px 30px #173a3012}.hero h1{margin:0 0 10px;font-size:34px}.hero p{color:#5a6965}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:24px}.card{background:#fff;border:1px solid #dde7e3;border-radius:12px;padding:20px}.card h2{margin:0 0 8px;font-size:17px}.card p{margin:0;color:#64716e;line-height:1.5}.status{display:inline-block;margin-top:16px;padding:8px 12px;border-radius:999px;background:#dcf5eb;color:#17694f;font-weight:700}
        @media(max-width:600px){.wrap{padding:18px 12px}.hero{padding:22px}.hero h1{font-size:27px}}
    </style>
</head>
<body>
<header class="topbar"><strong>{{ config('app.name') }}</strong></header>
<main class="wrap">
    <section class="hero">
        <h1>Gestão completa do condomínio</h1>
        <p>A base funcional já está preparada para autenticação por perfil, unidades e operações administrativas.</p>
        <span class="status">Sistema ativo</span>
    </section>
    <section class="grid">
        <article class="card"><h2>Acessos</h2><p>Controle de entradas e saídas.</p></article>
        <article class="card"><h2>Visitantes e QR Code</h2><p>Autorização e validação na portaria.</p></article>
        <article class="card"><h2>Encomendas</h2><p>Recebimento e retirada por unidade.</p></article>
        <article class="card"><h2>Reservas</h2><p>Conflito controlado e aprovação.</p></article>
        <article class="card"><h2>Comunicados</h2><p>Notícias, urgências e vencimentos.</p></article>
        <article class="card"><h2>Chamados</h2><p>Solicitações, comentários e status.</p></article>
        <article class="card"><h2>Financeiro</h2><p>Cobranças individuais e baixas manuais.</p></article>
        <article class="card"><h2>Cadastros</h2><p>Usuários, unidades e permissões.</p></article>
    </section>
</main>
</body>
</html>
