<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OperationsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', [OperationsController::class, 'dashboard'])->name('dashboard');
    Route::get('/visitantes', [OperationsController::class, 'visitors'])->name('visitors.index');
    Route::get('/visitantes/novo', [OperationsController::class, 'createVisitor'])->name('visitors.create');
    Route::post('/visitantes', [OperationsController::class, 'storeVisitor'])->name('visitors.store');
    Route::get('/portaria/visitantes/{token}', [OperationsController::class, 'validateVisitor'])->name('visitors.validate');
    Route::post('/portaria/visitantes/{visitor}/movimentar', [OperationsController::class, 'checkVisitor'])->name('visitors.check');
    Route::get('/reservas', [OperationsController::class, 'reservations'])->name('reservations.index');
    Route::get('/reservas/nova', [OperationsController::class, 'createReservation'])->name('reservations.create');
    Route::post('/reservas', [OperationsController::class, 'storeReservation'])->name('reservations.store');
    Route::patch('/reservas/{reservation}', [OperationsController::class, 'updateReservation'])->name('reservations.update');
    Route::get('/comunicados', [OperationsController::class, 'announcements'])->name('announcements.index');
    Route::get('/comunicados/novo', [OperationsController::class, 'createAnnouncement'])->name('announcements.create');
    Route::post('/comunicados', [OperationsController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::get('/chamados', [OperationsController::class, 'tickets'])->name('tickets.index');
    Route::get('/chamados/novo', [OperationsController::class, 'createTicket'])->name('tickets.create');
    Route::post('/chamados', [OperationsController::class, 'storeTicket'])->name('tickets.store');
    Route::get('/chamados/{ticket}', [OperationsController::class, 'showTicket'])->name('tickets.show');
    Route::post('/chamados/{ticket}/comentarios', [OperationsController::class, 'commentTicket'])->name('tickets.comment');
    Route::patch('/chamados/{ticket}', [OperationsController::class, 'updateTicket'])->name('tickets.update');
    Route::get('/encomendas', [OperationsController::class, 'deliveries'])->name('deliveries.index');
    Route::get('/encomendas/nova', [OperationsController::class, 'createDelivery'])->name('deliveries.create');
    Route::post('/encomendas', [OperationsController::class, 'storeDelivery'])->name('deliveries.store');
    Route::patch('/encomendas/{delivery}/retirada', [OperationsController::class, 'collectDelivery'])->name('deliveries.collect');
    Route::get('/acessos', [OperationsController::class, 'accesses'])->name('accesses.index');
    Route::post('/acessos', [OperationsController::class, 'storeAccess'])->name('accesses.store');
});

Route::get('/up', fn () => response()->json(['status' => 'ok']));
