<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Enzo Capitani',
            'email' => 'dentista.ec@odonto.test',
        ]);
        User::factory()->create([
            'name' => 'Henry Kanemoto',
            'email' => 'dentista.hk@odonto.test',
        ]);
        User::factory()->admin()->create([
            'name' => 'Emanoel Fernandes',
            'email' => 'dono.ef@owner.test',
        ]);
    }
}
