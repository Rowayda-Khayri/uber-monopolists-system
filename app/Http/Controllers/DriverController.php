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
        
        $errors = json_decode("{}"); // to return it as empty object not string if there are no errors
        $content = json_decode("{}"); // to return it as empty object not string if there is no content
        
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
            "errorMsgs" => $errors,
            "content" => $content
        ])->getContent();

        return stripslashes($json);
    }
    
    public function getMonopolists($time) {
        
        $errors = json_decode("{}"); // to return it as empty object not string if there are no errors
        $content = json_decode("{}"); // to return it as empty object not string if there is no content
        
        //calculate monopolists
        
        $tripsCounter = TripCounter::orderby('created_at', 'desc')
                ->get(['counter'])
                ->first();
        
        $monopolistCriterion = 10 * $tripsCounter->counter / 100;
        
//        if ($time == 1) { //month
//            
//        }else if ($time ==2) { //year
//            
//        }else { //all time
//            
//        }
        
        //get monopolists
        
        $monopolists = Driver::query()
                ->where('trips_counter', '>=', $monopolistCriterion)
                ->get([
                    'id',
                    'name',
                    'trips_counter'
                ]);
        
        $content->monopolists = $monopolists;
        
        header('Content-Type: application/json', true);
        
        $json = response::json([
            "msg"=>'success',
            "errorMsgs"=> $errors,
            "content"=> $content
        ])->getContent();
    
        return stripslashes($json);
    }
           
}