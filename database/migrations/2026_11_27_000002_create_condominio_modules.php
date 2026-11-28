<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('plate', 20)->nullable();
            $table->string('token', 64)->unique();
            $table->timestamp('valid_from');
            $table->timestamp('valid_until');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('area');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['pendente', 'aprovada', 'rejeitada', 'cancelada'])->default('pendente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['area', 'date', 'start_time']);
        });

        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->enum('priority', ['baixa', 'normal', 'alta', 'urgente'])->default('normal');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['aberto', 'em_atendimento', 'resolvido', 'fechado'])->default('aberto');
            $table->enum('priority', ['baixa', 'normal', 'alta'])->default('normal');
            $table->boolean('private')->default(true);
            $table->timestamps();
        });

        Schema::create('ticket_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('recipient');
            $table->string('courier')->nullable();
            $table->string('tracking_code')->nullable();
            $table->timestamp('received_at');
            $table->timestamp('collected_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('visitors');
    }
};
