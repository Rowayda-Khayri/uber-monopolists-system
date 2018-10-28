<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;
    
    protected $table = 'trips';
    
    public function driver(){
        
        return $this->belongsTo('App\Driver');
    }
}
