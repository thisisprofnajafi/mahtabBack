<?php

namespace App\Http\Controllers;

use App\Mail\UserSendCodeEmail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use http\Message;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if the user exists
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            // If the user doesn't exist, create a new user
            $user = new User;
            $user->email = $credentials['email'];
            $user->password = bcrypt($credentials['password']);
            $user->save();
        } else {
            // If the user exists, check if the password is correct
            if (!Hash::check($credentials['password'], $user->password)) {
                // Password is incorrect
                return redirect()->back()->with('error', 'Invalid credentials');
            }
        }

        // Log the user in
        auth()->login($user);

        // Redirect to the desired page after login
        return redirect()->route('index');
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if (!Hash::check($request->code, $user->code)) {
            return back()->with('error', 'Invalid code.');
        }

        if ($user->code_expire < Carbon::now()) {
            return back()->with('error', 'Code has expired.');
        }

        Auth::login($user);

        return redirect(route('index'));
    }
    public function loginPage(){

        if (Auth::check()){
            return redirect()->route('index');
        }

        return view('login');
    }

    public function codePage($email){
        return view('code')->with(['email'=>$email]);
    }
}
