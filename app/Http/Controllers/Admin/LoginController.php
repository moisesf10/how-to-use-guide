<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class LoginController extends Controller
{
    public function index(){

        return view('admin.authenticate.login');
    }

    public function authenticate(Request $request){

        $rules = [
            'login' =>  'required',
            'password'  =>  'required' . (($request->password != 'admin') ? '|min:8' : '')
        ];

        $messages = [
            'login.required'    =>  'Login ou senha inválido',
            'password.required'    =>  'Login ou senha inválido',
        ];

        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors(['Login ou senha inválido']);
        }

        $user = User::where('email', $request->login)
            ->whereRaw("status = 'active'")
            ->first();

        if(empty($user) || ! Hash::check($request->password, $user->password)  ){
            return redirect()->back()
                ->withInput($request->except('_token'))
                ->with('success', false)
                ->withErrors(['Login ou senha inválido']);
        }

        auth()->guard('admin')->login(User::find($user->id));
        return redirect()->intended(route('admin_index'));

    }

    public function logout(Request $request){
        auth()->guard('admin')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->route('admin_login');
    }

}
