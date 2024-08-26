<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\TokenPW;
use App\Http\Controllers\MailController;

class UserController extends Controller
{
    public function index(){
        return view('profile.index');
    }

    public function resetPassword(Request $request){
        try{
            $rota = "Configuracoes/index";
            if($request->password != $request->confirm_password){
                $mensagem = "as Senhas não Combinam!";
                $status = "error";
                return false;
            }

            User::find(Auth::user()->id)->update([
                "password"=> Hash::make($request->password)
            ]);
            $mensagem = "Senha Atualizada com Sucesso!";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $rota = "Configuracoes/index";
            $status = 'error';
        }finally{
            return redirect()->route($rota)->with($status, $mensagem);
        }
    }

    public function resetPasswordLink(Request $request){
        try{
            $rota="PasswordReset/Email";
            if(!User::where('email',$request->email)->exists()){
                $mensagem = "Email não Existente na Plataforma";
                $status="error";
                return false;
            }
            
            $RandPW = rand(1,999999);
            $TokenPW = base64_encode($RandPW);
            TokenPW::create([
                "email"=>$request->email,
                "token"=>$TokenPW
            ]);
            MailController::send($request->email,'Recuperação de Senha','Mail.resetpassword',array("link"=>route('Password/Reset',$TokenPW)));
            $mensagem = "Link de Recuperação Enviado para o E-mail";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = $th->getMessage();
            $status = "error";
            $rota = "PasswordReset/Email";
        }finally{
            return redirect()->route($rota)->with($status,$mensagem);
        }
    }

    public function newPasswordLink(Request $request){
        try{
            if($request->password != $request->confirm_password){
                $mensagem = "as Senhas não Combinam!";
                $status = "error";
                $rota = "Password/Reset";
                $tk = $request->TokenPW;
                return false;
            }

            if(!TokenPW::where('token',$request->TokenPW)->exists()){
                $mensagem = "Token Inválido";
                $status = "error";
                $rota = "login";
                $tk = "";
                return false;
            }

            $email = TokenPW::where('token',$request->TokenPW)->pluck('email');
            User::where('email',$email)->update([
                "password"=> Hash::make($request->password)
            ]);
            TokenPW::where('email',$email)->delete();
            $rota = "login";
            $tk = "";
            $mensagem = "Senha Atualizada com Sucesso!";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $rota = "Password/Reset";
            $status = 'error';
        }finally{
            return redirect()->route($rota,$tk)->with($status, $mensagem);
        }
    }

    public function resetPasswordLinkEmail($token){
        return view('auth.passwordreset',[
            "Token"=> TokenPW::where('token',$token)->first()
        ]);
    }

    public function resetPasswordLinkView(){
        return view('auth.newpassword');
    }
}
