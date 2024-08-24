<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Evento;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AvaliadoresController extends Controller
{
    public const submodulos = array([
        'nome' => 'Avaliadores',
        'rota' => 'Avaliadores/index',
        'endereco' => 'index'
    ]);
    public function index(){
        return view('Avaliadores.index',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            'Eventos'=> Evento::all()
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = User::where('Tipo',2)->where('id',$id)->first();
        }

        return view('Avaliadores.cadastro', $view);
    }

    public function delete(Request $request){
        return User::find($request->id)->where('Tipo',2)->delete();
    }

    public function apagaAvaliador($IDAvaliador){
        return User::find($IDAvaliador)->delete();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['tipo'] = 2;
            if(!$request->id){
                $RandPW = rand(100000,999999);
                $rota = 'Avaliadores/Novo';
                $aid = '';
                $data['password'] = Hash::make($RandPW);
                $Evento = Evento::find($request->IDEvento);
                MailController::send($request->email,'Confirmação - Avaliador','Mail.cadastroavaliador',array('Evento'=> $Evento->Titulo,'Senha'=> $RandPW,'Email'=> $request->email));
                User::create($data);
            }else{
                $rota = 'Avaliadores/Edit';
                if($request->alteraSenha){
                    $RandPW = rand(100000,999999);
                    $Evento = Evento::find($request->IDEvento);
                    $data['password'] = Hash::make($RandPW);
                    MailController::send($request->email,'Confirmação - Avaliador','Mail.cadastroavaliador',array('Senha'=> $RandPW,'Email'=> $request->email,'Evento'=> $Evento->Titulo));
                }
                $aid = $request->id;
                User::find($request->id)->update($data);
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Avaliadores/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function getAvaliadores(){
        $IDEvento = Session::get('IDEvento');
        $registros = DB::select("SELECT 
        u.name,
        u.email,
        u.id,
        CASE WHEN c.id IS NULL THEN 0 ELSE 1 END as Certificou,
        CASE WHEN e.id IS NULL THEN 0 ELSE 1 END as Avaliou
        FROM users u 
        LEFT JOIN certificados c ON(u.id = c.IDInscrito) 
        LEFT JOIN entergas e ON(u.id = e.IDAvaliador)
        WHERE u.tipo = 2
        ");
        if(count($registros) > 0){
            foreach($registros as $r){
                $ApagarAvaliador = '"'.route('Avaliadores/Excluir',$r->id).'"';
                $item = [];
                $item[] = $r->name;
                $item[] = $r->email;
                $item[] = "<a href=".route('Avaliadores/Edit',$r->id).">Abrir</a> <button class='btn btn-danger text-white btn-xs' onclick='apagarAvaliador($ApagarAvaliador,$r->Certificou,$r->Avaliou)'>Apagar</button>";
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
