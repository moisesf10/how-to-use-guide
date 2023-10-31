<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FileTrait;
use App\Models\Configuration;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralSettingsController extends Controller
{
    use FileTrait;

    public function index(){

        $configurations = config()->get('app.system');

        return view('admin.configurations.general')
            ->with('configurations',$configurations)
            ;
    }

    public function saveGeneral(Request $request){

        $rules = [
            'status_create_account'   =>  'required|in:active,email_pending,pending,create_inside'
        ];

        $messages = [
            'status_create_account.required' =>  'Por favor, informar o status de criação de conta',
            'status_create_account.in' =>  'O tipo de criação de conta é inválido',
        ];

        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }


        $config = Configuration::whereRaw("uid = 'general'")->first();

        $file = null;
        if($request->hasFile('logo_file')  ){
            $file = $this->uploadFile(file: $request->file('logo_file'),storageDirPath: 'app/public/images' );
        }



        DB::beginTransaction();
        try{
            $data = [
                'system_name'   =>  $request->system_name,
                'copyright' =>  $request->copyright,
                'status_create_account'  =>  $request->status_create_account
            ];


            if(! $config){
                $data['logo'] = $file ?? null;

                $config = Configuration::create([
                    'uid'   => 'general',
                    'name'  =>  'Configurações gerais',
                    'description'   =>  'Configurações gerais do sistema',
                    'content'   =>  $data
                ]);
            }else{
                if($file){
                    $data['logo'] = $file;
                }else{
                    $data['logo'] = $config->content->logo ?? null;
                }
                $config->content = $data;
                $config->save();
            }

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }
        }

        DB::commit();


        return redirect()->back()
            ->with('success', true)
            ->with('message', 'Configurações gerais salvas com sucesso');

    }

    public function saveSmtp(Request $request){
        $rules = [
            'port'   =>  'nullable|numeric'
        ];

        $messages = [
            'port.numeric' =>  'O número da porta é inválido',
        ];

        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }

        DB::beginTransaction();
        try{

            Configuration::updateOrCreate([
                'uid'   =>  'smtp'
            ],[
                'name'  => 'Configurações do SMTP',
                'description'   =>  'Configura o SMTP para o envio de e-mails',
                'content'   =>  [
                    'host'  =>  $request->host,
                    'port'  =>  $request->port,
                    'security'  =>  $request->security,
                    'login'  =>  $request->login,
                    'password'  =>  Crypt::encrypt($request->password)
                ]
            ]);

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }
        }

        DB::commit();


        return redirect()->back()
            ->with('success', true)
            ->with('message', 'As configurações do SMTP foram salvas com sucesso');
    }

    public function saveGoogle(Request $request){

        DB::beginTransaction();
        try{

            Configuration::updateOrCreate([
                'uid'   =>  'google'
            ],[
                'name'  => 'Configurações de APIs do google',
                'description'   =>  'Configura as chaves de apis do google',
                'content'   =>  [
                    'client_id'  =>  $request->client_id,
                    'secret_key'  =>  $request->secret_key,
                    'api_key'  =>  $request->api_key,
                ]
            ]);

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }
        }

        DB::commit();


        return redirect()->back()
            ->with('success', true)
            ->with('message', 'As configurações do google foram salvas com sucesso');

    }


}
