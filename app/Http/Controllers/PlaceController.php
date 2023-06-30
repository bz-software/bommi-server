<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    public function register(Request $request){
        $user = $request->get('usuario');
        $place = $request->get('estabelecimento');
        $placeAdress = $place['endereco'];
    
        
        return[
            'usuario' => $user,
            'place' => $place
        ];
    }
}
