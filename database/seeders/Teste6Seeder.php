<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Teste6Seeder extends Seeder
{
    public function run()
    {
        DB::table('teste6')->insert([
            'first_name' => 'John2',
            'last_name' => 'Doe2',
            'email' => 'john.doe2@example.com',
            'notas' => '1',
            'numero' => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
