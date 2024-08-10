<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Evento;
use Illuminate\Support\Facades\Hash;
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
                    $data['password'] = Hash::make($RandPW);
                    MailController::send($request->email,'Confirmação - Avaliador','Mail.cadastroavaliador',array('Senha'=> $RandPW,'Email'=> $request->email));
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
        $registros = User::where('Tipo',2)->get();
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->name;
                $item[] = $r->email;
                $item[] = "<a href=".route('Avaliadores/Edit',$r->id).">Abrir</a>";
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
