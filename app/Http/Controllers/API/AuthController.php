<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(Request $request){

        $validatedData = $request->validate([
           'nume' => 'max:55',
           'CNP' => 'required|unique:users|max:13'
        ]);

        //verificarea CNP-ului
        if($this->verificaCNP($request->CNP)) {
            $user =new User($validatedData);
            $accessToken = $user->createToken('authToken')->accessToken;
            return response(['user'=>$user, 'access_token'=>$accessToken],201);
        }
        else return response(['message'=>'CNP-ul este invalid!']);

    }

    public function login(Request $request){
        $loginData=$request->validate([
            'CNP'=>'required|max:13'
        ]);

        if(!auth()->attempt($loginData)){
            return response(['message'=>'CNP incorect sau neinregistrat'],400);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user'=>auth()->user(), 'access_token'=>$accessToken]);

    }

    //validarea CNP-ului doar dupa cifra de control
    public function verificaCNP(string $cnp){
        $arr=preg_split("",$cnp);
        $arrver=array(2,7,9,1,4,6,3,5,8,2,7,9);
        $sum=0;
        for($i=0;$i<12;$i++){
            $sum=$sum+intval($arr[$i])*$arrver[$i];
        }
        $cifra_control=$sum%11;
        if($cifra_control==intval($arr[12]))
            return true;
        else return false;
    }

}
