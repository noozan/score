<?php

namespace App\Helpers;

Class APIHelpers{

    public  static function APIResponse($isError,$code,$message,$content=null){
        $result=[];
        if($isError){
            $result['success']=false;
            $result['code']=$code;
            $result['message']=$message;
        }else{
            $result['success']=true;
            $result['code']=$code;
            if($content==null)
                $result['message']=$message;
            else
                $result['data']=$content;
        }
        return $result;

    }

}
