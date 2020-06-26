<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use Carbon;

class EquipamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $equip = DB::table('equipamentos')
            ->leftJoin('leituras', function ($join) {
                $join->on('equipamentos.id', '=', 'leituras.cod_equipamento')
                    ->whereRaw('leituras.id IN (select MAX(leituras.id) from leituras)')
                    ->groupBy('leituras.cod_equipamento');
            })
            ->where('equipamentos.cod_user', Auth::user()->id)
            ->select('equipamentos.id', 'equipamentos.apelido', 'leituras.solenoide as ativo', 'leituras.created_at as ultimaConexao', 'leituras.temperatura', 'leituras.humidade')
            ->groupBy('equipamentos.id')
            ->get();

        return $equip;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::table('parametros')->where('cod_equipamento', $id)
            ->update(['temperatura_max' => $request->temperatura_max, 'temperatura_min' => $request->temperatura_min, 'humidade_max' => $request->humidade_max, 'humidade_min' => $request->humidade_min, 'domingo' => $request->domingo, 'segunda' => $request->segunda, 'terca' => $request->terca, 'quarta' => $request->quarta, 'quinta' => $request->quinta, 'sexta' => $request->sexta, 'sabado' => $request->sabado,  'horario_ativar' => $request->horario_ativar,  'horario_desativar' => $request->horario_desativar, 'updated_at' => Carbon::now()]);

        DB::table('solenoide_status')->where('cod_equipamento', $id)
            ->update(['status' => false, 'manual' => false, 'updated_at' => Carbon::now()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
