<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;


class Driver extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    
    protected $table = 'drivers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password', 
        'general_trips_counter', 
        'month_trips_counter', 
        'year_trips_counter'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function trips(){
        
        return $this->hasMany('App\Trip');
    }
}
