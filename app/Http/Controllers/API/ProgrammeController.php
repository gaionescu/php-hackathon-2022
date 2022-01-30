<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgrammeResource;
use App\Models\Programme;
use App\Models\Sala;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;


class ProgrammeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clase=Programme::all();
        return response(['programmes' => ($clase),'message'=>'Incarcate cu succes'],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user=User::where('id',auth()->user()->getAuthIdentifier())->first();
        if($user->adminToken!=null) {
            $data = [$request->class, $request->sala_id, $request->locuri, $request->from, $request->to];
            $data = $request->all();
            $validator = Validator::make($data, [
                'class' => 'required|max:50|string',
                'sala_id' => 'required|integer',
                'locuri' => 'required|integer',
                'from' => 'required',
                'to' => 'required'
            ]);

            if($request->from<$request->to){
                return response(['message'=>'Ora de inceput trebuie sa fie inaintea orei final'],403);
            }
            
            $salavalidator = Sala::where('id', $request->sala_id)->first();
            if ($salavalidator == null) {
                return response(['message' => 'sala invalida'], 400);
            }

            if ($validator->fails()) {
                return response(['error' => $validator->errors(), 'Validation Error'], 403);
            }

            //
            $fromvalidation = DB::table('programmes')
                ->select()
                ->where('sala_id', $request->sala_id)
                ->whereBetween('from', [$request->from, $request->to])
                ->first();

            $tovalidation = DB::table('programmes')
                ->select()
                ->where('sala_id', $request->sala_id)
                ->whereBetween('to', [$request->from, $request->to])
                ->first();

            if ($fromvalidation == null && $tovalidation == null) {

                $programme = new Programme();
                $programme->class = $request->class;
                $programme->locuri = $request->locuri;
                $programme->sala_id = $request->sala_id;
                $programme->from = $request->from;
                $programme->to = $request->to;
                $programme->save();
                return response(['programme' => new ProgrammeResource($programme), 'message' => 'Creat cu succes'], 200);

            }
            return response(['message' => 'Sala este ocupata in intervalul orar'], 400);
        }
        else return response(['message','forbidden'],403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Programme  $programme
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'programme_id' => 'required',
        ]);
        if($validator->fails()){
            return response(['message'=>'There has been an error'],400);
        }

        $programme=Programme::where('id',$request->programme_id)->first();
        if($programme==null){
            return response(['message'=>'The requested programme was not found'],404);
        }

        return response(['programme'=>$programme, 'message'=>'Afisat cu succes'],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Programme  $programme
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Programme $programme)
    {
        $programme->update($request->all());

        return response(['programme'=>new ProgrammeResource($programme),'message'=>'Modificat cu succes'],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Programme  $programme
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user=User::where('id',auth()->user()->id)->first();
        if($user->adminToken!=null) {
            $programme=Programme::where('id',$request->id)->first();
            $programme->delete();
            return response(['message' => "Sters"]);
        }
        else return response(['message'=>'Forbidden'],403);
    }
}
