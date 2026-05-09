<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function index(){
        $users = User::all();   
        // return $users->paginate(10);
        return response()->json($users);
    }
    public function destroy($id){
        $user = User::findorFail($id);
        if($user->role!='admin')
            $user->delete();
        return response()->json([
            'message' => 'User deleted'
        ]);
    }
    public function update(Request $request, $id){
        $user = User::findorFail($id);
        $validated = $request->validate([
            'username'=> 'required',
            'role'=> 'required',
            'email'=> 'required',
        ]);
        $user->update($validated);
        response()->json();
    }
}
