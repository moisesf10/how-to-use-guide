<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function index(){


        return view('admin.account.create-account');
    }

    public function save(Request $request){

        $rules = [
            'name'  =>  'required',
            'email' =>  'required|email',
            'password'  =>  'required|min:8',
            'password_confirmation' =>  'required|same:password',
            'terms' =>  'required|in:yes'
        ];

        $messages = [
            'name.required' =>  'Por favor, informar o nome',
            'email.required'  => 'Por favor, informar um e-mail válido',
            'email.email'  => 'o e-mail informado é inválido',
            'password.required'  =>  'Por favor, informar uma senha',
            'password.min'  =>  'A senha precisa ter no mínimo :min caracteres',
            'password_confirmation.required'  =>  'Por favor, confirmar a senha',
            'password_confirmation.same'  =>  'A senha de confirmação está diferente',
            'terms.required'  =>  'Você precisa aceitar os termos e condições',
            'terms.in'  =>  'Você precisa aceitar os termos e condições',
        ];

        // efetua a validação
        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }

        $statusAccount = 'pending';

        $configurations = config()->get('app.system');
        $general = $configurations->where('uid', 'general')->first();
        if($general){
            $statusAccount = $general->content?->status_create_account ?? 'pending';
        }

        $user = null;
        DB::beginTransaction();
        try{
            $user = User::create([
                'name' =>  $request->name,
                'email' =>  $request->email,
                'password' =>  Hash::make($request->password),
                'status'    =>  $statusAccount
            ]);
        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao realizar o cadastro. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }

        }
        DB::commit();

        switch($user->status){
            case 'pending':
                // send to page of information about the approved for admin
                break;
            case 'email_validation':
                // send to page of information about the email validation
                break;
            case 'active':
                auth()->guard('admin')->login(User::find($user->id));
                if(! empty($request->redirect_after_login)){
                    $redirect = urldecode($request->redirect_after_login);
                    return redirect()->to($redirect);
                }else{
                    return redirect()->to(route('admin_index'));
                }
                break;
            default:
                // redirect to page of information about create account with disabled status
                break;
        }

    }

}
