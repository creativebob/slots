<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('slots')
            ->insert([
                [
                    'capacity' => 10,
                    'remaining' => 10,
                ],
                [
                    'capacity' => 1,
                    'remaining' => 1,
                ],
                [
                    'capacity' => 4,
                    'remaining' => 4,
                ],
            ]);
    }
}
