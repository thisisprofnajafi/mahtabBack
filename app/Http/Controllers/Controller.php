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

        $request->validate([
            'email' => 'required'
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (!$user) {
            $user = new User();
            $user->email = $request->email;
            $user->save();
        }

        $code = rand(1000, 9999);
        $user->code = Hash::make($code);
        $user->code_expire = Carbon::now()->addMinutes(10);
        $user->save();
            Mail::to($user->email)->send(new UserSendCodeEmail($code,$user->email));
        return redirect(route('code page',['email'=>$request->email]));

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
        return view('login');
    }

    public function codePage($email){
        return view('code')->with(['email'=>$email]);
    }
}
