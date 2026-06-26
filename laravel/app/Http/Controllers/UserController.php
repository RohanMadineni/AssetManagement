<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            // Http::post('localhost:3000/UserDeleted', $notification);
            Http::post(config('services.realtime.url') . '/UserDeleted', $notification);
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
        // Http::post('localhost:3000/UserUpdated', $notification);
        Http::post(config('services.realtime.url') . '/UserUpdated', $notification);
        $user->update($validated);
        return response()->json();
    }

    public function getProfile(){
        $user = User::findorFail(Auth::id());
        return response()->json($user);
    }

    public function updateProfile(Request $request){
        $user = User::findorFail(Auth::id());
       
        $user->update([
            'username' => $request->Name,
            'email' => $request->Email,
        ]);
        return response()->json($user);
    }

    public function updatePassword(Request $request){
        $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8'
            ]);
        $id = Auth::id();
        $user = User::findorFail($id);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {

            return response()->json([
                'message' => 'Current password is incorrect',
                'current' => $request->current_password, 
                'correct' => $user->password,
            ], 400);

        }

            // Update password
            // $user->password = Hash::make($request->new_password);

            $user->update([
                'password'=>Hash::make($request->new_password),
            ]);

            return response()->json([
                'message' => 'Password updated successfully'
            ]);
    }
}
