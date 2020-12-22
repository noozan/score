<?php

namespace App\Http\Controllers;

use App\Competition;
use App\Helpers\APIHelpers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompetitionController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user=$this->guard()->user();
    }
    //
    public function index(){

        $games=Competition::where('active',1)->get(['id','name','description']);
        return response()->json(APIHelpers::APIResponse(false,200,'',$games));
    }
    public function join(Request $request){
        if($this->user->competitions()->find($request->competition_id)){
            return response()->json(APIHelpers::APIResponse(true,0,'user already enrolled') );
}
        $validator= Validator::make($request->all(),
        ['competition_id'=>'exists:competitions,id']);
        if($validator->fails())
            return response()->json(APIHelpers::APIResponse(true,400,$validator->errors()) );

        //return response()->json(Competition::find($request->competition_id)->price);
        $competition_price= Competition::find($request->competition_id)->price;
        if($this->user->transactionsSum() >=$competition_price)
        {
            //DEDUCT AMOUNT FROM USER
            $this->user->competitions()->attach($request->competition_id);
            $transaction= new Transaction();
            $transaction->amount=$competition_price*-1;
            $this->user->transactions()->save($transaction);

            //dEPOSIT TO ADMIN
            $transaction= new Transaction();
            $transaction->amount=$competition_price*1;
            $transaction->user_id=1;
            $transaction->save();
            return response()->json(APIHelpers::APIResponse(false,1,'user joined') );
        }
        else{
            return response()->json(APIHelpers::APIResponse(true,0,'Insufficient balance. Please reload') );
        }



       return response()->json($this->user->transactionsSum());

        //$games=Competition::where('active',1)->get(['id','name','description']);
        return response()->json(APIHelpers::APIResponse(false,200,'success'));
    }

    protected function guard(){
        return Auth::guard('api');
    }

}
