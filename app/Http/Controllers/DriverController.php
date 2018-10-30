<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Driver;
use App\Trip;
use App\TripCounter;
use DateTime;
use Response;
use DB;
use Carbon\Carbon;

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
        
        //get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $driver = Driver::find($user->id);
        
        /**month trips counter**/
        
        // check if month counter should restart from 1
        if ($driver->updated_at->month == $currentMonth) { 
            
            //increment in the same month counter 
            $driver->increment('month_trips_counter'); 
            
        } else { // restart month counter
            
            $driver->month_trips_counter = 1;
            
            $driver->save(['timestamps'=>false]); // don't update updated_at to fix incrementing year
        }
        
        /**year trips counter**/
        
        // check if year counter should restart from 1
        if ($driver->updated_at->year == $currentYear) { 
            
            //increment in the same year counter 
            $driver->increment('year_trips_counter'); 
            
        } else { // restart year counter
            
            $driver->year_trips_counter = 1;
            $driver->updated_at = Carbon::now();
            $driver->save();
        }
        
        /**general trips counter**/
        
        $driver->increment('general_trips_counter');
        
        dd($driver);
        dd('done');
        
        //increment trips counters
        TripCounter::orderby('created_at', 'desc')
                ->first()
                ->update([
                'general_counter' => DB::raw('general_counter + 1'),
                'month_counter' => DB::raw('month_counter + 1'),
                'year_counter' => DB::raw('year_counter + 1')
            ]);
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