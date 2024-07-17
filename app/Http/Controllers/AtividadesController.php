<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Atividade;
use App\Models\Sala;
use Illuminate\Support\Facades\DB;
class AtividadesController extends Controller
{
    public const submodulos = array([
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

    public function index($IDEvento){
        return view('Atividades.index', [
            'submodulos' => self::submodulos,
            'IDEvento' => $IDEvento
        ]);
    }

    public function indexInscrito(){
        return view('Atividades.indexInscrito',[
            'Atividades' => DB::select("SELECT a.id,a.Titulo,a.Data,s.Sala,a.Descricao,a.Inicio,a.Termino FROM atividades a INNER JOIN salas s ON(a.IDSala = s.id)")
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

    public function atividade($IDEvento){
        return view('Salas.sala');
    }

    public function delete(Request $request){
        return Atividade::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            if(!$request->id){
                $rota = 'Eventos/Atividades/Novo';
                $aid = $request->IDEvento;
                Atividade::create($request->all());
            }else{
                $rota = 'Eventos/Atividades/Edit';
                $aid = array("IDEvento" => $request->IDEvento,"id"=>$request->id);
                Atividade::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Eventos/Atividades/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
            //dd($request->IDEvento);
        }
    }

    public function getAtividades($IDEvento){
        $registros = DB::select("SELECT s.IDEvento,a.id,a.Titulo,a.Data,s.Sala,a.Descricao,a.Inicio,a.Termino FROM atividades a INNER JOIN salas s ON(a.IDSala = s.id)");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Sala;
                $item[] = Controller::data($r->Data,'d/m/Y');
                $item[] = $r->Descricao;
                $item[] = $r->Inicio." - ".$r->Termino;
                $item[] = "<a href=".route('Eventos/Atividades/Edit',['id'=>$r->id,'IDEvento'=>$r->IDEvento])." class='btn bg-fr text-white btn-xs'>Abrir</a>";
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
