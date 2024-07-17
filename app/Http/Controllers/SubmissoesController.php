<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submissao;
use Illuminate\Support\Facades\DB;

class SubmissoesController extends Controller
{
    public const submodulos = array([
        'nome' => 'Submissoes',
        'rota' => 'Submissoes/index',
        'endereco' => 'index'
    ]);

    public function index(){
        return view('Submissoes.index', [
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Submissao::find($id);
        }

        return view('Submissoes.cadastro', $view);
    }

    public function delete(Request $request){
        return Submissao::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            if(!$request->id){
                $rota = 'Submissoes/Novo';
                $aid = '';
                Submissao::create($request->all());
            }else{
                $rota = 'Submissoes/Edit';
                $aid = $request->id;
                Submissao::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Submissoes/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function getSubmissoes(){
        $registros = DB::select("SELECT 
            e.Titulo as Evento,
            a.name as Avaliador,
            s.Titulo,
            s.Regras
        FROM submissoes s
        INNER JOIN eventos e ON(s.IDEvento = e.id)
        INNER JOIN users a ON(s.IDAvaliador = a.id)
        WHERE a.tipo = 2
        ");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Evento;
                $item[] = $r->Avaliador;
                $item[] = $r->Titulo;
                $item[] = $r->Regras;
                $item[] = "<a href=".route('Submissoes/Edit',$r->id).">Abrir</a>";
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
