<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $unit = Unit::firstOrCreate(['block' => 'A', 'number' => '101']);

        User::updateOrCreate(
            ['email' => 'admin@jampasolucoes.com.br'],
            [
                'name' => 'Administrador',
                'role' => 'administrador',
                'unit_id' => null,
                'phone' => null,
                'active' => true,
                'password' => Hash::make('Alterar123!'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'morador@exemplo.com'],
            [
                'name' => 'Morador Demonstrativo',
                'role' => 'morador',
                'unit_id' => $unit->id,
                'phone' => null,
                'active' => true,
                'password' => Hash::make('Alterar123!'),
            ]
        );
    }
}
