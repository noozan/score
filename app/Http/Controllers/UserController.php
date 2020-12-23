<?php

namespace App\Http\Controllers;

use App\Group;
use App\Helpers\APIHelpers;
use App\Transaction;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;
use function MongoDB\BSON\toJSON;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api',['except'=>['login','register']]);
    }

    public function login(Request $request){
        $token = auth()->attempt($request->all());
        //dd($token);

        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if($validator->fails())
            return response()->json(APIHelpers::APIResponse(false,400,'ok',$validator->errors()));

        $token_validity=24*60;

        auth('api')->factory()->setTTL($token_validity);

        if(!$token=$this->guard()->attempt($validator->validated())){
            return response()->json(APIHelpers::APIResponse(true,401,'unauth',null));

        }
        return response()->json(APIHelpers::APIResponse(false,0,'ok',$this->respondWithToken($token)));

    }

    public function logout(){

        $this->guard()->logout();
        return response()->json(APIHelpers::APIResponse(false,200,"logout successfully",null));

    }
    public function register(Request  $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|between:2,100',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed|min:6'
        ]);
        if($validator->fails())
            return response()->json(APIHelpers::APIResponse(false,0,'',$validator->errors()));

        $user=User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        return response()->json(APIHelpers::APIResponse(false,200,'',$user));


    }
    public function profile()
    {
        $data=[];
        //$user = $this->guard()->user()->competitions()->get(['id','name']);//User::find($this->guard()->user()->id);
        $data['user']=$this->guard()->user();

        $data['competitions']=$this->guard()->user()->competitions()->get();

        return \response()->json(APIHelpers::APIResponse(false, 1, '', $data));

    }
    public function refresh(){
        return $this->respondWithToken($this->guard()->refresh());
        //return \response()->json(APIHelpers::APIResponse(true,200,'',$code));

    }

    public function reload(Request $request){

        $validator=Validator::make($request->all(),
        [
            'amount'=>"required|regex:/^\d+(\.\d{1,2})?$/"
        ]);
        if($validator->fails())
            return response()->json(APIHelpers::APIResponse(true,400,$validator->errors()) );

        $transaction= new Transaction();
        $transaction->amount=$request->amount;
        $transaction->description='Recharge';
        $this->guard()->user()->transactions()->save($transaction);
        return \response()->json(APIHelpers::APIResponse(false, 1, 'Successfully reloaded '. $request->amount ));
    }
    protected function guard(){
        return Auth::guard('api');
    }

    protected function respondWithToken($token){
        return
            [
                'token'          => $token,
                'token_type'     => 'bearer',
                'token_validity' => (auth('api')->factory()->getTTL() * 60),
            ];


    }



    //


}
