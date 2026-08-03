<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Notification;
use App\Jobs\SendRealtimeNotification;

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
                // 'user' => $user,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
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
            'email'=>$request->email,
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $notification = Notification::create([
            'user_id' => Auth::id(),
            'title' => 'User Created',
            'message' => $user->username,
            'type' => 'success'
        ]);
        // Http::post('localhost:3000/UserCreated', $notification);
        Http::post(config('services.realtime.url') . '/UserCreated', $notification);
        // SendRealtimeNotification::dispatch('/UserCreated', $notification);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            // 'user' => $user,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
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
