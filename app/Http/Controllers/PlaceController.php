<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    public function register(Request $request){
        $ruser = $request->get('usuario');
        $place = $request->get('estabelecimento');
        $placeAdress = $place['endereco'];

        $userValidator = Validator::make($ruser, User::rules(), User::messages());
        if($userValidator->fails()){
            return response()->json(['errors' => $userValidator->errors()], 400);
        }

        $user = new User($ruser);
        $user->senha = Hash::make($user->senha);

        if($user->save()){
            return response()->json($user->id, 200);
        } 
    }
}
