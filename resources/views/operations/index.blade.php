@extends('operations.layout')

@section('content')
    <h1>{{ $title }}</h1>
    @if (in_array($kind, ['visitors', 'reservations', 'tickets', 'deliveries', 'accesses'], true) || ($kind === 'announcements' && auth()->user()->isAdmin()))
        <p><a class="btn" href="{{ route($kind . '.create') }}">Adicionar</a></p>
    @endif

    <div class="box">
        <table>
            <thead>
                <tr>
                    @if ($kind === 'visitors')
                        <th>Visitante</th><th>Unidade</th><th>Validade</th><th>Status</th>
                    @elseif ($kind === 'reservations')
                        <th>Espaço</th><th>Unidade</th><th>Data/horário</th><th>Status</th><th>Ação</th>
                    @elseif ($kind === 'announcements')
                        <th>Título</th><th>Prioridade</th><th>Publicado</th><th>Comunicado</th>
                    @elseif ($kind === 'tickets')
                        <th>Chamado</th><th>Unidade</th><th>Status</th><th>Prioridade</th>
                    @elseif ($kind === 'deliveries')
                        <th>Destinatário</th><th>Unidade</th><th>Recebido</th><th>Retirada</th><th>Ação</th>
                    @elseif ($kind === 'accesses')
                        <th>Horário</th><th>Ator</th><th>Ação</th><th>Unidade</th><th>Detalhes</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        @if ($kind === 'visitors')
                            <td>{{ $item->name }}<br><small>{{ $item->document }}</small></td>
                            <td>{{ $item->unit?->label() }}</td>
                            <td>{{ $item->valid_from->format('d/m/Y H:i') }}–{{ $item->valid_until->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($item->checked_out_at) Finalizado
                                @elseif ($item->checked_in_at) Dentro
                                @else Autorizado
                                @endif
                                @if (auth()->user()->isAdmin() || auth()->user()->isPortaria())
                                    <br><a href="{{ route('visitors.validate', $item->token) }}">Validar código</a>
                                @endif
                            </td>
                        @elseif ($kind === 'reservations')
                            <td>{{ $item->area }}</td><td>{{ $item->unit?->label() }}</td>
                            <td>{{ $item->date->format('d/m/Y') }}<br>{{ substr($item->start_time, 0, 5) }}–{{ substr($item->end_time, 0, 5) }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>
                                @if (auth()->user()->isAdmin() && $item->status === 'pendente')
                                    <form method="post" action="{{ route('reservations.update', $item) }}">@csrf @method('PATCH')
                                        <select name="status"><option value="aprovada">Aprovar</option><option value="rejeitada">Rejeitar</option></select>
                                        <button class="btn" type="submit">Salvar</button>
                                    </form>
                                @endif
                            </td>
                        @elseif ($kind === 'announcements')
                            <td>{{ $item->title }}</td><td>{{ ucfirst($item->priority) }}</td>
                            <td>{{ $item->published_at?->format('d/m/Y H:i') ?? 'Agora' }}</td><td>{{ $item->body }}</td>
                        @elseif ($kind === 'tickets')
                            <td><a href="{{ route('tickets.show', $item) }}">{{ $item->subject }}</a><br>{{ $item->user->name }}</td>
                            <td>{{ $item->unit?->label() }}</td><td>{{ str_replace('_', ' ', ucfirst($item->status)) }}</td><td>{{ ucfirst($item->priority) }}</td>
                        @elseif ($kind === 'deliveries')
                            <td>{{ $item->recipient }}<br>{{ $item->courier }}</td><td>{{ $item->unit?->label() }}</td>
                            <td>{{ $item->received_at->format('d/m/Y H:i') }}</td><td>{{ $item->collected_at?->format('d/m/Y H:i') ?? 'Aguardando' }}</td>
                            <td>
                                @if (! $item->collected_at)
                                    <form method="post" action="{{ route('deliveries.collect', $item) }}">@csrf @method('PATCH')
                                        <button class="btn" type="submit">Registrar retirada</button>
                                    </form>
                                @endif
                            </td>
                        @elseif ($kind === 'accesses')
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td><td>{{ $item->actor_name }}</td>
                            <td>{{ $item->action }}</td><td>{{ $item->unit?->label() ?? '—' }}</td><td>{{ $item->notes }}</td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $items->links() }}
    </div>

    @if ($kind === 'accesses')
        <div class="box" style="margin-top:16px">
            <h2>Registrar movimentação manual</h2>
            <form method="post" action="{{ route('accesses.store') }}">@csrf
                <label>Nome da pessoa</label><input name="actor_name" required>
                <label>Ação/movimentação</label><input name="action" required placeholder="entrada, saída, prestador...">
                <label>Portão</label><input name="gate" value="Portaria" required>
                <label>Unidade (ID, opcional)</label><input type="number" name="unit_id">
                <label>Observação</label><textarea name="notes"></textarea>
                <button class="btn" type="submit">Registrar</button>
            </form>
        </div>
    @endif
@endsection
