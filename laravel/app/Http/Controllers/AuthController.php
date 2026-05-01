<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request){

        $credentials = $request->only( 'username', 'password');
        
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $user = Auth::user();
        
        $token = $request->user()->createToken('token');
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token->plainTextToken,
                    'type' => 'bearer',
                    'expires_in' => 3600,
                ],
                
            ]);

    }

    public function register(Request $request){

        $user = User::create([
            'username'=> $request->username,
            'password'=> Hash::make($request->password),
            'role'=>$request->role,
        ]);

        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message'=>'logged out']);
    }

    public function getrole(){
        $user = Auth::user();
        return response()->json(['role' => $user->role, 'username'=>$user->username]);
    }
}
