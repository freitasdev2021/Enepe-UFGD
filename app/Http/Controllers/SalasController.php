<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;

class SalasController extends Controller
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
        return view('Salas.index', [
            'submodulos' => self::submodulos,
            'IDEvento' => $IDEvento
        ]);
    }

    public function cadastro($IDEvento,$id = null){
        $view = array(
            'id' => '',
            'IDEvento' => $IDEvento,
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::submodulos;
            $view['Registro'] = Sala::find($id);
        }

        return view('Salas.cadastro', $view);
    }

    public function delete(Request $request){
        return Sala::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            if(!$request->id){
                $rota = 'Eventos/Salas/Novo';
                $aid = $request->IDEvento;
                Sala::create($request->all());
            }else{
                $rota = 'Eventos/Salas/Edit';
                $aid = array("IDEvento" => $request->IDEvento,"id"=>$request->id);
                Sala::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Eventos/Salas/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
            //dd($request->IDEvento);
        }
    }

    public function getSalas($IDEvento){
        $registros = Sala::all()->where('IDEvento',$IDEvento);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Sala;
                $item[] = $r->Capacidade;
                $item[] = "<a href=".route('Eventos/Salas/Edit',['id'=>$r->id,'IDEvento'=>$r->IDEvento])." class='btn bg-fr text-white btn-xs'>Abrir</a>";
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
