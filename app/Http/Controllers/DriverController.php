<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Driver;
use App\Trip;
use DateTime;
use Response;

class DriverController extends Controller {
    
    private $driver;
    private $jwtauth;
    
    public function __construct(Driver $driver, JWTAuth $jwtauth){
        
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the login method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       $this->middleware('jwt.auth', ['except' => [
           'getMonopolists'
       ]]);
       
       $this->$driver= $driver;
       $this->jwtauth = $jwtauth;
    }
    
    public function acceptTrip(JWTAuth $jwtAuth) {
        
        $user = $jwtAuth->toUser($jwtAuth->getToken());
        
        //create new trip instance
        $newTrip = new Trip();
        $newTrip->driver_id = $user->id;
        $newTrip->save();
        
        //increment driver trips counter
        Driver::find($user->id)->increment('trips_counter');
        
        header('Content-Type: application/json', true);
        
        $json = response::json([
            "msg"=>'success',
            "errorMsgs"=> null,
            "content"=> null
        ])->getContent();

        return stripslashes($json);
    }
    
    public function getMonopolists($time) {
        
        
        
        
        if ($time == 1) { //month
            
        }else if ($time ==2) { //year
            
        }else { //all time
            
        }
        
        header('Content-Type: application/json', true);
        
        $json = response::json([
            "msg"=>'success',
            "errorMsgs"=> null,
            "content"=> null
        ])->getContent();
        
    }
           
}