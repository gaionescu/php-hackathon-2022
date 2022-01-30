<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Participari;
use App\Models\Programme;
use http\Env\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function participa(Request $request){

        $data=$request->all();
        $validatedData = Validator::Make($data,[
            'programme_id' => 'required',
        ]);
        $user_id=auth()->user()->getAuthIdentifier();

        if($validatedData->fails()){
            return response(['message','A aparut o eroare, nu s-a putut finaliza cererea de participare'],400);
        }

        //Verificam daca exista programme
        $programme=Programme::where('id',$request->programme_id)->first()->pluck('id')[0];
        if($programme==null){
            return response(['message','inexistent programme'],400);
        }


        //Verificam daca mai exista o participare la acelasi programme
        $validareExistenta=Participari::where('programme_id',$request->programme_id)->where('user_id',$user_id)->first();
        if($validareExistenta==null) {


            $programme_requested = Programme::where('id', $request->programme_id)->first();


            //Luam lista de participari si vedem daca vreuna din ele se suprapune cu cea actuala
            $participari = Participari::where('user_id', $user_id)->pluck('programme_id');
            for ($i = 0; $i < sizeof($participari); $i++) {
                $part = Programme::where('id', $participari[$i])->first();
                if (($part->from < $programme_requested->from && $part->to > $programme_requested->from) || ($part->from < $programme_requested->to && $part->to > $programme_requested->to)) {
                    return response(['message' => 'Error, you have a booking for another programme intersecting in the time interval'], 400);
                }
            }


            //verificam daca sunt ocupate toate locurile
            $nr_max = Programme::where('id', $request->programme_id)->pluck('locuri')[0];
            $nr_participanti = Participari::where('programme_id', $request->programme_id)->get()->count();
            if ($nr_max > $nr_participanti) {


                //Adaugam o participare la programme pentru user-ul logat
                $participare = new Participari();
                $participare->user_id = $user_id;
                $participare->programme_id = $request->programme_id;
                $participare->save();

                return response(['participare' => $participare, 'message' => 'cererea de participare a fost inregistrata cu succes'], 200);
            }else return response(['message','Toate locurile au fost ocupate'],400);

        }
        else return response(['message' => 'You are already a participant in this programme!'], 403);

    }

    public function anuleazaParticipare(Request $request){

        $data=$request->all();
        $validatedData = Validator::Make($data,[
            'programme_id' => 'required',
        ]);


        if($validatedData->fails()){
            return response(['message'=>'A aparut o eroare, nu se poate finaliza cererea'],400);
        }


        $user_id=auth()->user()->getAuthIdentifier();

        $participare=Participari::where('user_id',$user_id)->where('programme_id',$request->programme_id)->first();

        if($participare==null){
            return response(['message'=>'Nu poti anula o participare inexistenta'],400);
        }

        $participare->delete();
        return response(['message'=>'Cererea a fost efectuata cu succes'],200);
    }

}
