@extends('operations.layout')

@section('content')
    <h1>{{ $title }}</h1>
    <div class="box">
        <form method="post" action="{{ $kind === 'visitor' ? route('visitors.store') : ($kind === 'reservation' ? route('reservations.store') : ($kind === 'announcement' ? route('announcements.store') : ($kind === 'ticket' ? route('tickets.store') : route('deliveries.store')))) }}">
            @csrf

            @if (in_array($kind, ['visitor', 'reservation', 'ticket', 'delivery'], true))
                <label for="unit_id">Unidade</label>
                <select id="unit_id" name="unit_id" @if ($kind !== 'ticket') required @endif>
                    <option value="">Selecione</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->label() }}</option>
                    @endforeach
                </select>
            @endif

            @if ($kind === 'visitor')
                <label for="name">Nome completo</label><input id="name" name="name" value="{{ old('name') }}" required>
                <label for="document">Documento (opcional)</label><input id="document" name="document" value="{{ old('document') }}">
                <label for="phone">Telefone</label><input id="phone" name="phone" value="{{ old('phone') }}">
                <label for="vehicle">Veículo</label><input id="vehicle" name="vehicle" value="{{ old('vehicle') }}">
                <label for="plate">Placa</label><input id="plate" name="plate" value="{{ old('plate') }}">
                <label for="valid_from">Válido a partir de</label><input id="valid_from" type="datetime-local" name="valid_from" value="{{ old('valid_from', now()->format('Y-m-d\TH:i')) }}" required>
                <label for="valid_until">Válido até</label><input id="valid_until" type="datetime-local" name="valid_until" value="{{ old('valid_until', now()->addHours(8)->format('Y-m-d\TH:i')) }}" required>
            @elseif ($kind === 'reservation')
                <label for="area">Espaço</label><input id="area" name="area" value="{{ old('area') }}" required placeholder="Salão de festas">
                <label for="date">Data</label><input id="date" type="date" name="date" min="{{ today()->toDateString() }}" value="{{ old('date') }}" required>
                <label for="start_time">Início</label><input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required>
                <label for="end_time">Fim</label><input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required>
                <label for="notes">Observações</label><textarea id="notes" name="notes">{{ old('notes') }}</textarea>
            @elseif ($kind === 'announcement')
                <label for="title">Título</label><input id="title" name="title" value="{{ old('title') }}" required>
                <label for="body">Comunicado</label><textarea id="body" name="body" required>{{ old('body') }}</textarea>
                <label for="priority">Prioridade</label><select id="priority" name="priority"><option value="normal">Normal</option><option value="baixa">Baixa</option><option value="alta">Alta</option><option value="urgente">Urgente</option></select>
                <label for="published_at">Publicar em (vazio = agora)</label><input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at') }}">
                <label for="expires_at">Expira em (opcional)</label><input id="expires_at" type="datetime-local" name="expires_at" value="{{ old('expires_at') }}">
            @elseif ($kind === 'ticket')
                <label for="subject">Assunto</label><input id="subject" name="subject" value="{{ old('subject') }}" required>
                <label for="description">Descrição</label><textarea id="description" name="description" required>{{ old('description') }}</textarea>
                <label for="priority">Prioridade</label><select id="priority" name="priority"><option value="normal">Normal</option><option value="baixa">Baixa</option><option value="alta">Alta</option></select>
            @elseif ($kind === 'delivery')
                <label for="recipient">Destinatário</label><input id="recipient" name="recipient" value="{{ old('recipient') }}" required>
                <label for="courier">Transportadora/entregador</label><input id="courier" name="courier" value="{{ old('courier') }}">
                <label for="tracking_code">Código de rastreio</label><input id="tracking_code" name="tracking_code" value="{{ old('tracking_code') }}">
            @endif

            <button class="btn" type="submit" style="margin-top:16px">Salvar</button>
        </form>
    </div>
@endsection
