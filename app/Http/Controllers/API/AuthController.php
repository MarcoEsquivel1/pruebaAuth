<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = Hash::make($request->password);
        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([
            'message' => 'Usuario creado correctamente',
            'data' => [
                'user' => $user,
                'access_token' => $accessToken
            ]
        ]);
    }

    public function login(Request $request){
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if(!auth()->attempt($loginData)){
            return response(['message' => 'Invalid credential']);
        }
         /** @var \App\Models\User */
        $user = Auth::user();
        $accessToken = $user->createToken('Access Token')->accessToken;

        return response([
            'message' => 'Login correcto',
            'data'=> [
                'user' => auth()->user(), 
                'access_token'=> $accessToken
                ]
            ]);
    }

    public function logout(Request $request)
    {        
        if (Auth::check()) {
            $token = Auth::user()->token();
            $token->revoke();
            return response(
                [
                    'message' => 'Sesion cerrada',
                    'data' => [
                        
                    ]
                ]
            );
        } 
        else{ 
            return response(
                [
                    'message' => 'Error'
                ]
            );
        } 
    }
}
