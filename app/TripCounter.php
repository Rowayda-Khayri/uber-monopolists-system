<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripCounter extends Model
{
    use SoftDeletes;
    
    protected $table = 'trips_counters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'general_counter', 
        'month_counter', 
        'year_counter'
    ];
}
