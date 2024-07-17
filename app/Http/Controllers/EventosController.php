<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Inscricao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class EventosController extends Controller
{
    public const submodulos = array([
        'nome' => 'Eventos',
        'rota' => 'Eventos/index',
        'endereco' => 'index'
    ]);

    public const cadastroSubmodulos = array([
        'nome' => 'Eventos',
        'rota' => 'Eventos/Edit',
        'endereco' => 'Cadastro'
    ],[
        'nome' => 'Salas',
        'rota' => 'Eventos/Salas/index',
        'endereco' => 'Salas'
    ],[
        'nome' => 'Atividades',
        'rota' => 'Eventos/Atividades/index',
        'endereco' => 'Atividades'
    ]);

    public function index(){
        $data = [
            'submodulos' => self::submodulos,
            'id' => ''
        ];
        $view = 'Eventos.index';
        if(Auth::user()->tipo == 3){
            $view = 'Eventos.indexInscrito';
            $data['Eventos'] = DB::select("SELECT 
                e.Titulo as Evento,
                e.id as IDEvento,
                e.Descricao as Descricao,
                CASE WHEN e.id = i.IDEvento THEN 1 ELSE 0 END AS Inscrito
                FROM eventos e
                LEFT JOIN inscricoes i ON(i.IDEvento = e.id)
            ");
        }
        return view($view,$data);
    }

    public function cadastro($id=null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Registro'] = Evento::find($id);
        }

        return view('Eventos.cadastro',$view);
    }

    public function inscricao($IDEvento){
        return view('Eventos.inscricao',[
            'Evento' => Evento::find($IDEvento)
        ]);
    }

    public function inscrever(Request $request){
        try{
            $data = $request->all();
            $mensagem = 'Inscrição Concluida! O Comprovante será Enviado via Email';
            $data['IDUser'] = Auth::user()->id;
            $aid = '';
            $rota = 'Eventos/index';
            $status = 'success';
            Inscricao::create($data);
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDEvento;
            $rota = 'Eventos/Inscricao';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function desinscrever($id){
        Inscricao::find($id)->delete();
    }

    public function delete(Request $request){
        return Evento::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            if(!$request->id){
                $rota = 'Eventos/Novo';
                $aid = '';
                Evento::create($request->all());
            }else{
                $rota = 'Eventos/Edit';
                $aid = $request->id;
                Evento::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Eventos/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getEventos(){
        $registros = Evento::all();
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Descricao;
                $item[] = Controller::data($r->Inicio,'d/m/Y');
                $item[] = Controller::data($r->Termino,'d/m/Y');
                $item[] = "<a href=".route('Eventos/Edit',$r->id)." class='btn bg-fr text-white btn-xs'>Abrir</a>";
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
