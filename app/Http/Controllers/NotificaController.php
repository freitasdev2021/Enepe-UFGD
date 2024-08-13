<?php

namespace App\Http\Controllers;
use App\Http\Controllers\MailController;
use App\Models\User;
use Illuminate\Http\Request;

class NotificaController extends Controller
{
    public function notificacoes(Request $request){
        $Emails = [];
        foreach(User::where('tipo',3)->get() as $p){
            array_push($Emails,$p->email);
        }
        MailController::sendMass($request->Remetente,$Emails,$request->Titulo,'Mail.notificacao',["Assunto"=>$request->Titulo,'Conteudo'=>$request->Notificacao]);
        return redirect()->route('dashboard');
    }
}
