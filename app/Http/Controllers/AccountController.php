<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    //
    public function register()
    {
        return view('account.register');
    }
    public function processRegister(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:5',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails())
        {
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('account.login')->with('success','you have successfully login');
    }

    public function login()
    {
        return view('account.login'); 
    }

    public function authenticate(Request $request){
        //dd($request);

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails())
        {
            return redirect()->route('account.login')->withErrors($validator);
        }

        if(Auth::attempt(['email' => $request->email , 'password' => $request->password]))
        {
            return view('account.profile');
        }
        else
        {
            return redirect()->route('account.login')->with('error','mismatch');
        }

    }

    public function profile()
    {
        $user = User::find(Auth::user()->id);
        return view('account.profile',[
            'user' => $user
        ]); 
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login')->with('success','successfully logout');
    }
    public function updateProfile(Request $request)

    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',''.Auth::user()->id.'','id'
        ]);

        if($validator->fails())
        {
            return redirect()->route('account.profile')->withInput->withErrors($validator);

        }

        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return redirect()->route('account.profile')->withInput->with('success','succesfully updated');


    }
}
