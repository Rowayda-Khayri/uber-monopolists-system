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
        
        /*=============================================*/
        /**********increment driver's counters*********/
        /*=============================================*/
        
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
            $driver->save();
        }
        
        /**general trips counter**/
        
        $driver->increment('general_trips_counter');
        
        /*=============================================*/
        /**********increment trips counters************/
        /*=============================================*/
        
        $tripCounter = TripCounter::orderby('created_at', 'desc')
                ->get()
                ->first();
        
        /**month counter**/
        
        // check if month counter should restart from 1
        if ($tripCounter->updated_at->month == $currentMonth) { 
            
            //increment in the same month counter 
            $tripCounter->increment('month_counter'); 
            
        } else { // restart month counter
            
            $tripCounter->month_counter = 1;
            
            $tripCounter->save(['timestamps'=>false]); // don't update updated_at to fix incrementing year
        }
        
        /**year counter**/
        
        // check if year counter should restart from 1
        if ($tripCounter->updated_at->year == $currentYear) { 
            
            //increment in the same year counter 
            $tripCounter->increment('year_counter'); 
            
        } else { // restart year counter
            
            $tripCounter->year_counter = 1;
            $tripCounter->save();
        }
        
        /**general trips counter**/
        
        $tripCounter->increment('general_counter');
        
        //update monopolists live list
        
        $monopolists = $this->getMonopolists(3);
        
        event(new \App\Events\MonopolistsUpdate($monopolists));
        
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

        if ($time == 1) { //month
            
            $tripsCounter = TripCounter::orderby('created_at', 'desc')
                    ->get(['month_counter'])
                    ->first();
            
            $monopolistCriterion = 10 * $tripsCounter->counter / 100;
        
            //get monopolists

            $monopolists = Driver::query()
                    ->where('month_trips_counter', '>=', $monopolistCriterion)
                    ->get([
                        'id',
                        'name',
                        'month_trips_counter'
                    ]);
        } else if ($time ==2) { //year
            
            $tripsCounter = TripCounter::orderby('created_at', 'desc')
                    ->get(['year_counter'])
                    ->first();
            
            $monopolistCriterion = 10 * $tripsCounter->counter / 100;
        
            //get monopolists

            $monopolists = Driver::query()
                    ->where('year_trips_counter', '>=', $monopolistCriterion)
                    ->get([
                        'id',
                        'name',
                        'year_trips_counter'
                    ]);
        } else if ($time ==3) { //all time
            
            $tripsCounter = TripCounter::orderby('created_at', 'desc')
                    ->get(['general_counter'])
                    ->first();
            
            $monopolistCriterion = 10 * $tripsCounter->counter / 100;
        
            //get monopolists

            $monopolists = Driver::query()
                    ->where('general_trips_counter', '>=', $monopolistCriterion)
                    ->get([
                        'id',
                        'name',
                        'general_trips_counter'
                    ]);
        } else {
            
            abort(404);
        }
        
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