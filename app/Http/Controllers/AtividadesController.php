<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ZoomController;
use App\Models\Atividade;
use App\Models\Sala;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DyteController;
class AtividadesController extends Controller
{
    public const submodulos = array([
        'nome' => 'Eventos',
        'rota' => 'Eventos/Edit',
        'endereco' => 'Cadastro'
    ],[
        'nome' => 'Atividades',
        'rota' => 'Eventos/Atividades/index',
        'endereco' => 'Atividades'
    ],[
        'nome' => 'Inscrições',
        'rota' => 'Eventos/Inscricoes',
        'endereco' => 'Inscricoes'
    ]);

    public function index($IDEvento){
        return view('Atividades.index', [
            'submodulos' => self::submodulos,
            'IDEvento' => $IDEvento
        ]);
    }

    public function indexInscrito(){
        $AND = '';
        if(Session::has('IDEvento')){
            $AND = ' WHERE a.IDEvento='.Session::get('IDEvento');
        }
        return view('Atividades.indexInscrito',[
            'Atividades' => DB::select("SELECT a.id,a.Titulo,a.Descricao,a.Inicio FROM atividades a $AND")
        ]);
    }

    public function cadastro($IDEvento,$id = null){
        $view = array(
            'id' => '',
            'IDEvento' => $IDEvento,
            'submodulos' => self::submodulos,
            'Salas' => Sala::all()->where('IDEvento',$IDEvento)
        );

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::submodulos;
            $view['Registro'] = Atividade::find($id);
        }

        return view('Atividades.cadastro', $view);
    }

    public function atividade($IDAtividade){
        return view('Salas.sala',[
            'Sala' => Atividade::find($IDAtividade),
            'Nome' => Auth::user()->name
        ]);
    }

    public function delete(Request $request){
        return Atividade::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            if(!$request->id){

                //dd($accessToken);
                ////
                $rota = 'Eventos/Atividades/Novo';
                $aid = $request->IDEvento;
                $meetingData = [
                    "title" => $request->Titulo,
                    "preferred_region" => "ap-south-1",
                    "record_on_start" => false,
                    "live_stream_on_start" => false
                ];

                $meeting = DyteController::createMeeting($meetingData);
                //$met = json_decode($meeting,true);
                //dd($meeting);
                $data['PWMeeting'] = 123;
                $data['IDMeeting'] = $meeting['data']['id'];
                $data['URLMeeting'] = 'urltest';
                
                Atividade::create($data);
            }else{
                $rota = 'Eventos/Atividades/Edit';
                $aid = array("IDEvento" => $request->IDEvento,"id"=>$request->id);
                Atividade::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDEvento;
            $rota = 'Eventos/Atividades/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
            //dd($request->IDEvento);
        }
    }

    public function getAtividades($IDEvento){
        $registros = DB::select("SELECT a.IDEvento,a.id,a.Titulo,a.Descricao,a.Inicio FROM atividades a");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Descricao;
                $item[] = Controller::data($r->Inicio,'d/m/Y H:i');
                $item[] = "<a href=".route('Eventos/Atividades/Edit',['id'=>$r->id,'IDEvento'=>$r->IDEvento])." class='btn bg-fr text-white btn-xs'>Abrir</a>
                <a href=".route('Atividades/Atividade',$r->id)." class='btn bg-fr text-white btn-xs'>Entrar</a>
                ";
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
