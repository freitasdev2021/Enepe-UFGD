<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Palestra;
use App\Models\Palestrante;
use Illuminate\Support\Facades\DB;
class PalestrasController extends Controller
{
    public const submodulos = array([
        "nome" => "Palestras",
        "rota" => "Palestras/index",
        "endereco" => 'index'
    ]);

    public const submodulosPalestrantes = array([
        "nome" => "Palestrantes",
        "rota" => "Palestrantes/index",
        "endereco" => 'index'
    ]);
    public function index(){
        return view('Palestras.index',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function indexPalestrantes(){
        return view('Palestrantes.index',[
            'submodulos' => self::submodulosPalestrantes,
            'id' => ''
        ]);
    }

    public function cadastro($id=null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Palestra::find($id);
        }

        return view('Palestras.cadastro', $view);
    }

    public function cadastroPalestrantes($id=null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Palestra::find($id);
        }

        return view('Palestrantes.cadastro', $view);
    }

    public function delete(Request $request){
        return Palestra::find($request->id)->delete();
    }

    public function deletePalestrantes(Request $request){
        return Palestra::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            if(!$request->id){
                $rota = 'Palestras/Novo';
                $aid = '';
                Palestra::create($request->all());
            }else{
                $rota = 'Palestras/Edit';
                $aid = $request->id;
                Palestra::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Palestras/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function savePalestrantes(Request $request){
        try{
            if(!$request->id){
                $rota = 'Palestrantes/Novo';
                $aid = '';
                Palestrante::create($request->all());
            }else{
                $rota = 'Palestrantes/Edit';
                $aid = $request->id;
                Palestrante::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Palestrantes/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function getPalestras(){
        $registros = DB::select("SELECT pl.*,pa.Nome FROM palestras pl INNER JOIN palestrantes pa ON(pl.IDPalestrante = pa.id)");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Nome;
                $item[] = $r->Palestra;
                $item[] = Controller::data($r->Data,'d/m/Y');
                $item[] = $r->Inicio;
                $item[] = $r->Termino;
                $item[] = "<a href=".route('Palestras/Edit',$r->id).">Abrir</a>";
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

    public function getPalestrantes(){
        $registros = Palestrante::all();
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Curriculo;
                $item[] = "<a href=".route('Palestrantes/Edit',$r->id).">Abrir</a>";
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
