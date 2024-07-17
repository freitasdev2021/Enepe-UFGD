<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = User::find($id)->where('Tipo',2);
        }

        return view('Avaliadores.cadastro', $view);
    }

    public function delete(Request $request){
        return User::find($request->id)->where('Tipo',2)->delete();
    }

    public function save(Request $request){
        try{
            if(!$request->id){
                $rota = 'Avaliadores/Novo';
                $aid = '';
                User::create($request->all());
            }else{
                $rota = 'Avaliadores/Edit';
                $aid = $request->id;
                User::find($request->id)->update($request->all());
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
