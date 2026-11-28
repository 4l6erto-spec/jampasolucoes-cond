<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->string('block')->nullable();
            $table->string('number');
            $table->integer('floor')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['block', 'number']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('role')->default('morador')->after('email');
            $table->string('phone', 25)->nullable()->after('role');
            $table->boolean('active')->default(true)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'role', 'phone', 'active']);
        });
        Schema::dropIfExists('units');
    }
};
