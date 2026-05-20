<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;

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
        if($user->role!='admin'){

            $notification = Notification::create([
                'user_id' => Auth::id(),
                'title' => 'User Deleted',
                'message' => $user->username,
                'type' => 'success'
            ]);
            Http::post('localhost:3000/UserDeleted', $notification);

            $user->delete();
        }
        
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
        $notification = Notification::create([
            'user_id' => Auth::id(),
            'title' => 'User Updated',
            'message' => $user->username,
            'type' => 'success'
        ]);
        Http::post('localhost:3000/UserUpdated', $notification);

        $user->update($validated);
        response()->json();
    }
}
