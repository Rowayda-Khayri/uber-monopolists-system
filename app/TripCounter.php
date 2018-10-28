<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripCounter extends Model
{
    use SoftDeletes;
    
    protected $table = 'trips_counter';

}
