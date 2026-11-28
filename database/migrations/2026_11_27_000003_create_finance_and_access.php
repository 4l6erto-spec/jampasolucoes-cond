<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->enum('status', ['pendente', 'pago', 'atrasado', 'cancelado'])->default('pendente');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('access_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gate');
            $table->string('action');
            $table->string('actor_name')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
        Schema::dropIfExists('charges');
    }
};
