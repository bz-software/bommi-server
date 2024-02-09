<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ControllerHelper as cHelper;

class AuthEmployeeController extends Controller
{
    public function __construct(){
        $this->middleware('auth:employee', ['except' => [
            'create',
            'login'
        ]]);
    }

    public function login(Request $request){

        $email = $request->input('emp_email');
        $password = $request->input('emp_password');

        $employeeFoundedByEmail = Employee::where('emp_email', $email)->where('emp_deleted', false)->first();

        if(empty($employeeFoundedByEmail)){
            return response()->json([
                'message' => 'User not found'
            ],401);
        }

        $login = auth('employee')->attempt(
            [
                'emp_email' => $email,
                'password' => $password
            ], true
        );

        
        if(!$login){
            return response()->json(['errors' => "Invalid Credentials"], 400);
        }else{
            return [
                'usuario' => auth('employee')->user(),
                'teste' => auth('employee')->check(),
                'token' => $login
            ];
        }

        // return false;
    }

    public function create(Request $request){
        $empValidator = Validator::make($request->all(), Employee::rules(), Employee::messages());

        if($empValidator->fails()){
            $messages = cHelper::getAllFirstMessages($empValidator->errors()->toArray());
            return response()->json(['errors' => $messages], 400);
        }

        $employeeSave = new Employee($request->all());
        $employeeSave->emp_password = Hash::make($request->input('emp_password'));
        $employeeSave->save();

        $login = auth('employee')->attempt(
            [
                'emp_email' => $request->input('emp_email'),
                'password' => $request->input('emp_password')
            ], true
        );

        if(!$login){
            return response()->json(['errors' => "error"], 400);
        }else{

            return [
                'usuario' => auth('employee')->user(),
                'teste' => auth('employee')->check()
            ];
        }
    }

    public function logout(Request $request){
        auth('employee')->logout();
        return response()->json(['success' => true], 200);
    }

    public function refresh(Request $request){
        return [
            'usuario' => auth('employee')->user(),
            'teste' => auth('employee')->check(),
            'token' => auth('employee')->refresh()
        ];
    }

    public function unauthorized(){
        return [
            'usuario' => auth('employee')->user(),
            'teste' => auth('employee')->check()
        ];
    }
}
