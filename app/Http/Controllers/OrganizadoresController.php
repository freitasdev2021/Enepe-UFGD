<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Banca;
use Illuminate\Support\Facades\Session;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrganizadoresController extends Controller
{
    public const submodulos = array([
        'nome' => 'Organizadores',
        'rota' => 'Organizadores/index',
        'endereco' => 'index'
    ]);
    public function index(){
        return view('Organizadores.index',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            "Eventos"=>Evento::all()
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = User::where('Tipo',1)->where('id',$id)->first();
        }

        return view('Organizadores.cadastro', $view);
    }

    public function apagaOrganizador($IDOrganizador){
        return User::find($IDOrganizador)->delete();
    }

    public function delete(Request $request){
        return User::find($request->id)->where('Tipo',1)->delete();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['tipo'] = 1;
            if(!$request->id){
                $RandPW = rand(100000,999999);
                $rota = 'Organizadores/Novo';
                $aid = '';
                $data['password'] = Hash::make($RandPW);
                if(User::where('email',$request->email)->exists()){
                    User::where('email',$request->email)->first();
                    Banca::where('IDUser')->update(['IDEvento'=>Session::get('IDEvento')]);
                }else{
                    $Evento = Evento::find(Session::get('IDEvento'));
                    MailController::send($request->email,'Confirmação - Organizador','Mail.cadastroorganizador',array('Evento'=> $Evento->Titulo,'Senha'=> $RandPW,'Email'=> $request->email));
                    $User = User::create($data);
                    Banca::create([
                        "IDUser"=> $User->id,
                        "IDEvento"=> Session::get('IDEvento'),
                        "Tipo"=> 1
                    ]);
                }
            }else{
                $rota = 'Organizadores/Edit';
                if($request->alteraSenha){
                    $RandPW = rand(100000,999999);
                    $data['password'] = Hash::make($RandPW);
                    $Evento = Evento::find(Session::get('IDEvento'));
                    MailController::send($request->email,'Confirmação - Organizador','Mail.cadastroorganizador',array('Evento'=> $Evento->Titulo,'Senha'=> $RandPW,'Email'=> $request->email));
                }

                if(!empty(Session::get('IDEvento')) && !Banca::where('IDEvento',Session::get('IDEvento'))->exists()){
                    Banca::where('IDUser',$request->id)->update([
                        "IDEvento"=>Session::get('IDEvento')
                    ]);
                    $Evento = Evento::find(Session::get('IDEvento'));
                    MailController::send($request->email,'Confirmação - Organizador','Mail.cadastroorganizador',array('Evento'=> $Evento->Titulo,'Senha'=> 'A Mesma','Email'=> $request->email));
                }
                $aid = $request->id;
                User::find($request->id)->update($data);
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Organizadores/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function getOrganizadores(){
        $currentId = Auth::user()->id;
        $IDEvento = Session::get('IDEvento');
        $registros = DB::select("SELECT 
        u.name,
        u.email,
        u.id,
        CASE WHEN c.id IS NULL THEN 0 ELSE 1 END as Certificou
        FROM users u 
        LEFT JOIN certificados c ON(u.id = c.IDInscrito) 
        WHERE u.tipo = 1 AND u.id != $currentId AND u.id IN(SELECT IDUser FROM bancaevento WHERE bancaevento.IDEvento = $IDEvento)
        ");
        if(count($registros) > 0){
            foreach($registros as $r){
                $ApagarOrganizador = '"'.route('Organizadores/Excluir',$r->id).'"';
                $item = [];
                $item[] = $r->name;
                $item[] = $r->email;
                $item[] = "<a href=".route('Organizadores/Edit',$r->id).">Abrir</a> <button class='btn btn-danger text-white btn-xs' onclick='apagarOrganizador($ApagarOrganizador,$r->Certificou)'>Apagar</button>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($registros)),
            "recordsFiltered" => intval(count($registros)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
}
