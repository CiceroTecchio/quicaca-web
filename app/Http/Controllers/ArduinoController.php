<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Auth;

class ArduinoController extends Controller
{
    public function status($id)
    {
        $status = DB::table('solenoide_status')
            ->where('solenoide_status.cod_equipamento', $id)
            ->select('solenoide_status.status as ativo')
            ->get();

        return $status;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $equipamento = $request->equipamento;
        $vazao = $request->vazao;
        $temperatura = $request->temperatura;
        $solenoide = $request->solenoide;
        $humidade = $request->humidade;

        $ativar_solenoide = false;

        DB::table('leituras')->insert(
            ['cod_equipamento' => $equipamento, 'vazao_agua' => $vazao, 'temperatura' => $temperatura, 'solenoide' => $solenoide, 'humidade' => $humidade]
        );

        $manual = DB::table('solenoide_status')
            ->where('cod_equipamento', $equipamento)
            ->pluck('manual')
            ->first();

        if ($manual == false) {


            $diasSemana = [
                'domingo',
                'segunda',
                'terca',
                'quarta',
                'quinta',
                'sexta',
                'sabado'
            ];

            $diaHoje = $diasSemana[Carbon::now()->dayOfWeek];
            $hora = Carbon::now()->format('H:i:s');

            $parametros = DB::table('parametros')
                ->where('cod_equipamento', $request->equipamento)
                ->get();

            for ($x = 0; $x < sizeof($parametros); $x++) {
                // if (($parametros[$x]->temperatura_min != null && $parametros[$x]->humidade_max != null) && ($parametros[$x]->temperatura_min >= $temperatura || $parametros[$x]->humidade_max <= $humidade)) {
                //     $ativar_solenoide = false;
                // }else 
                if ($parametros[$x]->$diaHoje == true && $parametros[$x]->horario_ativar <= $hora && $parametros[$x]->horario_desativar > $hora) {
                    $ativar_solenoide = true;
                } else if (($parametros[$x]->temperatura_max != null && $parametros[$x]->humidade_min != null) && $parametros[$x]->temperatura_max <= $temperatura || $parametros[$x]->humidade_min >= $humidade) {
                    $ativar_solenoide = true;
                }
            }

            if ($ativar_solenoide == true && $solenoide == false) {
                DB::table('solenoide_status')->where('cod_equipamento', $equipamento)
                    ->update(
                        ['status' => true]
                    );
                return 'asdf';
            } else if ($ativar_solenoide == false && $solenoide == true) {
                DB::table('solenoide_status')->where('cod_equipamento', $equipamento)
                    ->update(
                        ['status' => false]
                    );
            }
        }
        response()->json(['success' => 'success'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $status = DB::table('solenoide_status')
            ->join('equipamentos','solenoide_status.cod_equipamento','=', 'equipamentos.id')
            ->join('parametros', 'equipamentos.id', '=', 'parametros.cod_equipamento')
            ->where('solenoide_status.cod_equipamento', $id)
            ->select('equipamentos.apelido', 'solenoide_status.manual','solenoide_status.status as ativo', 'parametros.*')
            ->get();

        return $status;
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
        $equipUser = DB::table('equipamentos')->where('id', $id)->select('cod_user')->first();

        if ($equipUser->cod_user == \Auth::user()->id) {
            DB::table('solenoide_status')->where('cod_equipamento', $id)
                ->update(['status' => $request->status, 'manual' => true, 'updated_at' => Carbon::now()]);

            return response($request->status, 200);
        } else {

            return response('Acesso não autorizado', 403);
        }
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
