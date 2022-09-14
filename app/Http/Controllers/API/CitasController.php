<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CitasResource;
use App\Models\cita;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitasController extends Controller
{
    public function FormatD($date){
        $nDate = DateTime::createFromFormat('Y-m-d', $date)->format('d-m-Y');
        return $nDate;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $citas = cita::all();

        $citasF =  $citas->map(
            function($c){
                $c->fecha = $this->FormatD($c->fecha);
                return $c;
            }, $citas
        );

        return response(['message' => 'Se recupero correctamente',
        'data' => CitasResource::collection($citasF)], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'clientName' => 'required|max:100',
            'fecha' => 'date',
            'hora' => 'date_format:H:i',
            'servicio' => 'required|max:200'
        ]);

        if($validator->fails()){
            return response([
                'message' => 'Validation Fail',
                'data' => $validator->errors()
            ], 400);
        }

        $cita = cita::create($data);

        return response([
            'message' => 'Guardado Correctamente',
            'data' => [
                'cita' => new CitasResource($cita)
            ]], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function show(cita $citum)
    {
        return response([
            'message' => 'Se recupero correctamente',
            'data' => [
                'cita' => new CitasResource($citum)
            ]], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, cita $citum)
    {
        $citum->update($request->all());

        return response([
            'message' => 'Se actualizo correctamente',
            'data' => [
                'cita' => new CitasResource($citum)
            ]], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function destroy(cita $citum)
    {
        $citum->delete();

        return response([
            'message' => 'Se elimino correctamente'
        ], 200);
    }
}