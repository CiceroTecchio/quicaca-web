<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Str;

class UserController extends Controller
{
    public function login(Request $request){
        $user = DB::table('users')
                ->where('email', $request->email)
                ->get()
                ->first();

        if($user != null && Hash::check($request->senha, $user->password)){
            $token = Str::random(60);
            DB::table('users')->where('id', $user->id)->update(['api_token' => $token]);

            return response($token, 200);

        }else{
            return response('Dados Incorretos', 400);
        }
    }
}
