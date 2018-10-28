<?php

use Illuminate\Database\Seeder;
use App\TripCounter;

class TripCounterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TripCounter::firstOrCreate([
        'id' => 1,
        'counter' => 0
        ]);
    }
}
