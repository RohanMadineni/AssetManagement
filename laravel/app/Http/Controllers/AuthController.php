<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use app\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Arr;

// class AuthController extends Controller
// {
//     //
//     public function register(Request $request)
//     {
//         $data = $request->validate([
//             'name' => ['required','string','max:100'],
//             'email' => ['required','email','max:150','unique:users,email'],
//             'password' => ['required','string','min:8'],
//             'role' => ['sometimes','in:admin,manager,viewer'] // only admins can set role, enforce via policy if needed
//         ]);

//         // If only admins may set roles, override here for public registration:
//         $data['role'] = 'viewer';

//         $user = User::create([
//             'name' => $data['name'],
//             'email' => $data['email'],
//             'password' => bcrypt($data['password']),
//             'role' => $data['role'],
//         ]);

//         $token = $user->createToken('auth_token', ['asset:read','category:read'])->plainTextToken;

//         return response()->json([
//             'user' => Arr::except($user->toArray(), ['password']),
//             'access_token' => $token,
//             'token_type' => 'Bearer',
//         ], 201);
//     }

//     public function login(Request $request)
//     {
//         $credentials = $request->validate([
//             'email' => ['required','email'],
//             'password' => ['required','string'],
//         ]);
//         $user = User::where('email', $credentials['email'])->first();

//         if (!$user || !Hash::check($request->password, $user->password)) {
//             return response()->json(['message' => 'Invalid credentials'], 401);
//         }

        
//         // Issue abilities based on role
//         $abilities = match ($user->role) {
//             'admin'   => ['*'],
//             'manager' => ['asset:read','asset:create','asset:update','category:read','parameter:read'],
//             default   => ['asset:read','category:read','parameter:read'],
//         };

//         $token = $user->createToken('auth_token', $abilities)->plainTextToken;

//         return response()->json([
//             'user' => Arr::except($user->toArray(), ['password']),
//             'access_token' => $token,
//             'token_type' => 'Bearer',
//         ]);
//     }

//     public function logout(Request $request)
//     {
//         $request->user()->tokens()->delete();
//         return response()->json(['message' => 'Logged out']);
//     }
    
//}

use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;

class AuthController extends Controller {
    use HasApiTokens, HasFactory, Notifiable;

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'in:admin,manager,viewer'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'viewer'
        ]);

        return response()->json(['user' => $user], 201);
    }
    
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email','password'))) {
            return response()->json(['message'=>'Invalid credentials'],401);
        }

        $user = $request->user();
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }
}