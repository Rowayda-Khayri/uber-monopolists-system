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
        'general_counter' => 0,
        'month_counter' => 0,
        'year_counter' => 0
        ]);
    }
}
