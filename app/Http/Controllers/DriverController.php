<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Driver;
use App\Trip;
use App\TripCounter;
use DateTime;
use Response;

class DriverController extends Controller {
    
    private $driver;
    private $jwtauth;
    
    public function __construct(Driver $driver, JWTAuth $jwtauth) {
        
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
        
        //increment trips counter
        TripCounter::orderby('created_at', 'desc')
                ->first()
                ->increment('counter');
        
        header('Content-Type: application/json', true);
        
        $json = response::json([
            "msg" => 'success',
            "errorMsgs" => null,
            "content" => null
        ])->getContent();

        return stripslashes($json);
    }
    
    public function getMonopolists($time) {
        
        //calculate monopolists
        
        $tripsCounter = TripCounter::orderby('created_at', 'desc')
                ->get(['counter'])
                ->first();
        
        $monopolists = array(); //to add monopolists
        
        $drivers = Driver::query()
                ->get([
                    'id',
                    'name',
                    'trips_counter'
                ]);
        
        $monopolistCriterion = 10 * $tripsCounter->counter / 100;
        
        foreach ($drivers as $driver) { 
            
            //check if driver is a monopolist
            
            if ($driver->trips_counter >= $monopolistCriterion) {
                
                array_push($monopolists, $driver);
            }
        }
        
//        if ($time == 1) { //month
//            
//        }else if ($time ==2) { //year
//            
//        }else { //all time
//            
//        }
        
        $content = json_decode("{}"); // to return it as empty object not string if there is no content
        $content->monopolists = $monopolists;
        
        header('Content-Type: application/json', true);
        
        $json = response::json([
            "msg"=>'success',
            "errorMsgs"=> null,
            "content"=> $content
        ])->getContent();
    
        return stripslashes($json);
    }
           
}