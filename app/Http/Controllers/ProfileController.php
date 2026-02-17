<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(){
        $user=Auth()->user();
       return view("/profile",compact("user"));
    }

    public function change_password(Request $request){

        $user=Auth()->user();
    $request->validate([
        'old_password'=>'required|string|',
        'new_password'=>'required|string|min:8 |confirmed',
    ]);

    $hash=md5($request->old_password);

    if(!Hash::check($request->old_password,$user->password)){
        return reesponse()->witherrors("please enter the correct old password");
    }
    $password=Hash::make($request->new_password);
    $update=User::find(auth()->id());
    $update=$update->update(['password'=>$password]);

    
    return back()->with('success', 'Password updated successfully');
    }

}
