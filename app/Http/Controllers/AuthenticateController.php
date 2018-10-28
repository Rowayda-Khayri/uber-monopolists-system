<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Response;
use App\Driver;

class AuthenticateController extends Controller {
    
    private $driver;
    private $jwtauth;
    
    public function __construct(Driver $driver, JWTAuth $jwtauth){
        
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the login method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       $this->middleware('jwt.auth', ['except' => [
           'login',
           'register'
           ]]);
       
       $this->$driver= $driver;
       $this->jwtauth = $jwtauth;
    }
  
    public function register(Request $request) {

        // create the validation rules ------------------------
        $rules = array(                        
            'email'            => 'required|email|unique:drivers',     
            'password'         => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        );

        // do the validation ----------------------------------
        // validate against the inputs from our form
        $validator = Validator::make(Input::all(), $rules);


        // check if the validator failed -----------------------
        if ($validator->fails()) {

            // get the error messages from the validator

            $errors = $validator->errors();

            $errorsJSON =$errors->toJson();

            return $errorsJSON;

        } else {
            // validation successful ---------------------------

            //save to db
            $password=Hash::make($request->input('password'));

            $newDriver['password'] = $password;
            $newDriver['email'] = $request->email;
            $newDriver['name'] = $request->name;
            
            Driver::create($newDriver);
            
            return $this->login($request);

        }

    }
    
    public function login(Request $request) {
        
        $errors = json_decode("{}"); // to return it as empty object not string if there are no errors
        $content = json_decode("{}"); // to return it as empty object not string if there are no content

        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                
                $errors->error = "invalid_credentials";
            
                header('Content-Type: application/json', true);

                $json = response::json([
                    "msg"=>'failure',
                    "errorMsgs"=>$errors,
                    "content"=> $content
                ] , 401)->getContent();
            
                return stripslashes($json);
            }
        } catch (JWTException $e) {
            // something went wrong
            $json = response::json([
                "msg"=>'failure',
                "errorMsgs"=>$errors,
                "content"=> $content

            ] , 500)->getContent();

            return stripslashes($json);
        }
        
        // if no errors are encountered we can return a JWT
        
        $content->token = $token;
        
        header('Content-Type: application/json', true);
        
        $json = response::json([
            "msg"=>'success',
            "errorMsgs"=>$errors,
            "content"=> $content
        ])->getContent();

        return stripslashes($json);
    }
    
    public function logout(JWTAuth $jwtAuth) {
        
        JWTAuth::invalidate(JWTAuth::getToken());
        
        header('Content-Type: application/json', true);
        
        $json = response::json([
            "msg"=>'success',
            "errorMsgs"=>null,
            "content"=> null
        ])->getContent();
        
        return stripslashes($json);

    }
    
   
}



