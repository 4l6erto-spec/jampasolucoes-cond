<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Announcement;
use App\Models\Delivery;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Unit;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OperationsController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $unitId = $user->unit_id;
        return view('dashboard', [
            'visitorCount' => Visitor::query()->when(! $user->isAdmin() && ! $user->isPortaria(), fn ($q) => $q->where('unit_id', $unitId))->whereNull('checked_out_at')->count(),
            'deliveryCount' => Delivery::query()->when(! $user->isAdmin() && ! $user->isPortaria(), fn ($q) => $q->where('unit_id', $unitId))->whereNull('collected_at')->count(),
            'reservationCount' => Reservation::query()->when(! $user->isAdmin(), fn ($q) => $q->where('unit_id', $unitId))->where('date', '>=', today())->count(),
            'ticketCount' => Ticket::query()->when(! $user->isAdmin(), fn ($q) => $q->where('unit_id', $unitId))->whereNotIn('status', ['resolvido', 'fechado'])->count(),
        ]);
    }

    public function visitors(Request $request)
    {
        $q = Visitor::with('unit', 'authorizer')->latest();
        if (! $request->user()->isAdmin() && ! $request->user()->isPortaria()) $q->where('unit_id', $request->user()->unit_id);
        return view('operations.index', ['title' => 'Visitantes', 'items' => $q->paginate(20), 'kind' => 'visitors']);
    }

    public function createVisitor() { return view('operations.form', ['title' => 'Autorizar visitante', 'kind' => 'visitor', 'units' => $this->availableUnits()]); }

    public function storeVisitor(Request $request)
    {
        $data = $request->validate(['unit_id' => ['required', 'exists:units,id'], 'name' => ['required', 'string', 'max:255'], 'document' => ['nullable', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:40'], 'vehicle' => ['nullable', 'string', 'max:100'], 'plate' => ['nullable', 'string', 'max:20'], 'valid_from' => ['required', 'date'], 'valid_until' => ['required', 'date', 'after:valid_from']]);
        $this->assertUnitAccess((int) $data['unit_id'], $request);
        $data['token'] = Str::random(48);
        $data['authorized_by'] = $request->user()->id;
        $visitor = Visitor::create($data);
        return redirect()->route('visitors.index')->with('success', 'Visitante autorizado. Código de validação: '.$visitor->token);
    }

    public function validateVisitor(string $token)
    {
        $visitor = Visitor::with('unit')->where('token', $token)->firstOrFail();
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isPortaria() || auth()->user()->unit_id === $visitor->unit_id, 403);
        return view('operations.visitor-check', compact('visitor'));
    }

    public function checkVisitor(Request $request, Visitor $visitor)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isPortaria(), 403);
        $request->validate(['action' => ['required', Rule::in(['in', 'out'])]]);
        if ($request->action === 'in') {
            if ($visitor->checked_in_at || $visitor->checked_out_at || now()->lt($visitor->valid_from) || now()->gt($visitor->valid_until)) return back()->withErrors(['action' => 'Autorização inválida, fora da validade ou já utilizada.']);
            $visitor->update(['checked_in_at' => now()]);
            $this->logAccess($request, $visitor->unit_id, 'visitor_entry', $visitor->name, 'Visitante validado');
        } else {
            if (! $visitor->checked_in_at || $visitor->checked_out_at) return back()->withErrors(['action' => 'Não há entrada aberta para este visitante.']);
            $visitor->update(['checked_out_at' => now()]);
            $this->logAccess($request, $visitor->unit_id, 'visitor_exit', $visitor->name, 'Saída registrada');
        }
        return back()->with('success', 'Movimentação registrada.');
    }

    public function reservations(Request $request)
    {
        $q = Reservation::with('unit', 'user')->latest('date');
        if (! $request->user()->isAdmin()) $q->where('unit_id', $request->user()->unit_id);
        return view('operations.index', ['title' => 'Reservas', 'items' => $q->paginate(20), 'kind' => 'reservations']);
    }

    public function createReservation() { return view('operations.form', ['title' => 'Solicitar reserva', 'kind' => 'reservation', 'units' => $this->availableUnits()]); }

    public function storeReservation(Request $request)
    {
        $data = $request->validate(['unit_id' => ['required', 'exists:units,id'], 'area' => ['required', 'string', 'max:150'], 'date' => ['required', 'date', 'after_or_equal:today'], 'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i', 'after:start_time'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $this->assertUnitAccess((int) $data['unit_id'], $request);
        $overlap = Reservation::where('area', $data['area'])->whereDate('date', $data['date'])->whereIn('status', ['pendente', 'aprovada'])->where('start_time', '<', $data['end_time'])->where('end_time', '>', $data['start_time'])->exists();
        if ($overlap) return back()->withInput()->withErrors(['start_time' => 'Este espaço já possui uma reserva ou solicitação nesse horário.']);
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'pendente';
        Reservation::create($data);
        return redirect()->route('reservations.index')->with('success', 'Solicitação de reserva registrada.');
    }

    public function updateReservation(Request $request, Reservation $reservation)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['status' => ['required', Rule::in(['aprovada', 'rejeitada', 'cancelada'])]]);
        if ($data['status'] === 'aprovada') {
            $conflict = Reservation::where('area', $reservation->area)->whereDate('date', $reservation->date)->whereKeyNot($reservation->id)->whereIn('status', ['pendente', 'aprovada'])->where('start_time', '<', $reservation->end_time)->where('end_time', '>', $reservation->start_time)->exists();
            if ($conflict) return back()->withErrors(['status' => 'Há conflito de horário; não é possível aprovar.']);
        }
        $reservation->update($data);
        return back()->with('success', 'Reserva atualizada.');
    }

    public function announcements()
    {
        $items = Announcement::with('user')->where(function ($q) { $q->whereNull('published_at')->orWhere('published_at', '<=', now()); })->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))->latest()->paginate(20);
        return view('operations.index', ['title' => 'Comunicados', 'items' => $items, 'kind' => 'announcements']);
    }

    public function createAnnouncement() { abort_unless(auth()->user()->isAdmin(), 403); return view('operations.form', ['title' => 'Novo comunicado', 'kind' => 'announcement']); }

    public function storeAnnouncement(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'body' => ['required', 'string', 'max:10000'], 'priority' => ['required', Rule::in(['baixa', 'normal', 'alta', 'urgente'])], 'published_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date', 'after_or_equal:published_at']]);
        $data['user_id'] = $request->user()->id;
        Announcement::create($data);
        return redirect()->route('announcements.index')->with('success', 'Comunicado publicado/agendado.');
    }

    public function tickets(Request $request)
    {
        $q = Ticket::with('user', 'unit')->latest();
        if (! $request->user()->isAdmin()) $q->where('unit_id', $request->user()->unit_id);
        return view('operations.index', ['title' => 'Chamados', 'items' => $q->paginate(20), 'kind' => 'tickets']);
    }

    public function createTicket() { return view('operations.form', ['title' => 'Abrir chamado', 'kind' => 'ticket', 'units' => $this->availableUnits()]); }

    public function storeTicket(Request $request)
    {
        $data = $request->validate(['unit_id' => ['nullable', 'exists:units,id'], 'subject' => ['required', 'string', 'max:255'], 'description' => ['required', 'string', 'max:10000'], 'priority' => ['required', Rule::in(['baixa', 'normal', 'alta'])]]);
        if (! $request->user()->isAdmin()) $data['unit_id'] = $request->user()->unit_id;
        elseif ($data['unit_id']) $this->assertUnitAccess((int) $data['unit_id'], $request);
        $data['user_id'] = $request->user()->id;
        $data['private'] = true;
        Ticket::create($data);
        return redirect()->route('tickets.index')->with('success', 'Chamado registrado.');
    }

    public function showTicket(Request $request, Ticket $ticket)
    {
        $this->assertTicketAccess($request, $ticket);
        $ticket->load('user', 'unit', 'comments.user');
        return view('operations.ticket', compact('ticket'));
    }

    public function commentTicket(Request $request, Ticket $ticket)
    {
        $this->assertTicketAccess($request, $ticket);
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $ticket->comments()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);
        return back()->with('success', 'Comentário adicionado.');
    }

    public function updateTicket(Request $request, Ticket $ticket)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['status' => ['required', Rule::in(['aberto', 'em_atendimento', 'resolvido', 'fechado'])], 'priority' => ['sometimes', Rule::in(['baixa', 'normal', 'alta'])]]);
        $ticket->update($data);
        return back()->with('success', 'Chamado atualizado.');
    }

    public function deliveries(Request $request)
    {
        $q = Delivery::with('unit', 'receiver')->latest();
        if (! $request->user()->isAdmin() && ! $request->user()->isPortaria()) $q->where('unit_id', $request->user()->unit_id);
        return view('operations.index', ['title' => 'Encomendas', 'items' => $q->paginate(20), 'kind' => 'deliveries']);
    }

    public function createDelivery() { abort_unless(auth()->user()->isAdmin() || auth()->user()->isPortaria(), 403); return view('operations.form', ['title' => 'Registrar encomenda', 'kind' => 'delivery', 'units' => Unit::where('active', true)->orderBy('block')->orderBy('number')->get()]); }

    public function storeDelivery(Request $request)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isPortaria(), 403);
        $data = $request->validate(['unit_id' => ['required', 'exists:units,id'], 'recipient' => ['required', 'string', 'max:255'], 'courier' => ['nullable', 'string', 'max:255'], 'tracking_code' => ['nullable', 'string', 'max:255']]);
        $data['received_at'] = now(); $data['received_by'] = $request->user()->id;
        $delivery = Delivery::create($data);
        $this->logAccess($request, $delivery->unit_id, 'delivery_received', $delivery->recipient, 'Encomenda registrada: '.$delivery->id);
        return redirect()->route('deliveries.index')->with('success', 'Encomenda recebida e registrada.');
    }

    public function collectDelivery(Request $request, Delivery $delivery)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isPortaria() || $request->user()->unit_id === $delivery->unit_id, 403);
        if ($delivery->collected_at) return back()->withErrors(['delivery' => 'Esta encomenda já foi retirada.']);
        $delivery->update(['collected_at' => now(), 'collected_by' => $request->user()->id]);
        $this->logAccess($request, $delivery->unit_id, 'delivery_collected', $request->user()->name, 'Encomenda retirada: '.$delivery->id);
        return back()->with('success', 'Retirada registrada.');
    }

    public function accesses(Request $request)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isPortaria(), 403);
        $items = AccessLog::with('unit', 'user')->latest()->paginate(30);
        return view('operations.index', ['title' => 'Registros de acesso', 'items' => $items, 'kind' => 'accesses']);
    }

    public function storeAccess(Request $request)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isPortaria(), 403);
        $data = $request->validate(['unit_id' => ['nullable', 'exists:units,id'], 'actor_name' => ['required', 'string', 'max:255'], 'action' => ['required', 'string', 'max:100'], 'gate' => ['required', 'string', 'max:100'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $this->logAccess($request, $data['unit_id'] ?? null, $data['action'], $data['actor_name'], $data['notes'] ?? null, $data['gate']);
        return back()->with('success', 'Acesso registrado.');
    }

    private function logAccess(Request $request, ?int $unitId, string $action, ?string $actor, ?string $notes, string $gate = 'Portaria'): void
    {
        AccessLog::create(['unit_id' => $unitId, 'user_id' => $request->user()->id, 'gate' => $gate, 'action' => $action, 'actor_name' => $actor, 'ip_address' => $request->ip(), 'notes' => $notes]);
    }

    private function availableUnits()
    {
        $user = auth()->user();
        return $user->isAdmin() || $user->isPortaria() ? Unit::where('active', true)->orderBy('block')->orderBy('number')->get() : Unit::whereKey($user->unit_id)->get();
    }

    private function assertUnitAccess(int $unitId, Request $request): void
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isPortaria() || $request->user()->unit_id === $unitId, 403);
    }

    private function assertTicketAccess(Request $request, Ticket $ticket): void
    {
        abort_unless($request->user()->isAdmin() || $request->user()->id === $ticket->user_id || ($ticket->unit_id && $request->user()->unit_id === $ticket->unit_id), 403);
    }
}
