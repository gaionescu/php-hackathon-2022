<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Sala;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Sala::factory(1)->create([
            'nume'=>'sala 1',
            ]);

        Sala::factory(1)->create([
            'nume'=>'sala 2',
        ]);

        Sala::factory(1)->create([
            'nume'=>'sala 3',
        ]);

        Sala::factory(1)->create([
            'nume'=>'sala 4',
        ]);

        Sala::factory(1)->create([
            'nume'=>'sala 5',
        ]);

        Sala::factory(1)->create([
            'nume'=>'sala 6',
        ]);

        User::factory(1)->create([
           'CNP'=>'5220113098591',
            'nume'=>'administrator',
            'adminToken'=>'chiar_este_admin',
        ]);
    }
}
