<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api',['except'=>['login','register']]);
    }

    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if($validator->fails())
            return response()->json($validator->errors(),400);

        $token_validity=24*60;

        auth('api')->factory()->setTTL($token_validity);

        if(!$token=$this->guard()->attempt($validator->validated())){
            return response()->json(['error'=>'unauth'],401);

        }
        return $this->respondWithToken($token);

    }

    public function logout(){

    }
    public function register(Request  $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|between:2,100',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed|min:6'
        ]);
        if($validator->fails())
            return response()->json($validator->errors(),422);

        $user=User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));


    }
    public function profile(){

    }

    public function refresh(){

    }
    protected function guard(){
        return Auth::guard();
    }

    protected function respondWithToken($token){
        return response()->json([
           'token'=>$token,
            'token_type'=>'bearer',
            'token_validity'=>auth('api')->factory()->getTTL() * 60
        ]);
    }



    //


}
