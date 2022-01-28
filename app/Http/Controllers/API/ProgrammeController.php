<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgrammeResource;
use App\Models\Programme;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

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
        return response(['programmes' => ProgrammeResource::collection($clase),'message'=>'Incarcate cu succes'],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();

        $validator=Validator::make($data,[
            'clasa' => 'required|max:50',
            'sala' =>'required',
            'locuri' =>'required',
            'from' =>'required',
            'to'=>'required'
        ]);

        if($validator->fails()){
            return response(['error'=>$validator->errors(),'Validation Error']);
        }

        $programme=Programme::create($data);
        return response(['programme'=>new ProgrammeResource($programme),'message'=>'Creat cu succes'],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Programme  $programme
     * @return \Illuminate\Http\Response
     */
    public function show(Programme $programme)
    {
        return response(['programme'=>new ProgrammeResource($programme), 'message'=>'Afisat cu succes'],200);
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
    public function destroy(Programme $programme)
    {
        $programme->delete();

        return response(['message'=>"Sters"]);
    }
}
