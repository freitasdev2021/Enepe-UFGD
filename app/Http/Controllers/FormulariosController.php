<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use App\Models\Formulario;
use App\Models\Resposta;

class FormulariosController extends Controller
{
    public const submodulos = array([
        'nome' => 'Formulários',
        'rota' => 'Formularios/index',
        'endereco' => 'index'
    ]);

    public const cadastroSubmodulos = array([
        'nome' => 'Formulários',
        'rota' => 'Formularios/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Respostas',
        'rota' => 'Formularios/Respostas',
        'endereco' => 'Respostas'
    ]);
    public function index(){
        return view('Formularios.index',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            'Eventos' => Evento::all()
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Formulario::find($id);
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Formulario'] = json_decode($view['Registro']->Formulario);
        }

        return view('Formularios.cadastro', $view);
    }

    public function respostas($id){
        $Registro = DB::select("SELECT f.Formulario FROM formularios f WHERE f.id = $id")[0];
        return view('Formularios.respostas',array(
            "submodulos" => self::cadastroSubmodulos,
            "respostas" => json_decode($Registro->Formulario),
            "id" => $id
        ));
    }

    public function getRespostas($id){
        $registros = DB::select("
            SELECT r.Respostas, r.id, u.name 
            FROM respostas r 
            INNER JOIN formularios f ON (f.id = r.IDForm) 
            INNER JOIN users u ON (r.IDUser = u.id) 
            WHERE f.id = :id", ['id' => $id]);

            $itensJSON = [];

            if (count($registros) > 0) {
                foreach ($registros as $registro) {
                    $item = [];
                    // Adiciona o nome do usuário
                    $item[] = $registro->name;
                    
                    // Decodifica as respostas JSON para um array associativo
                    $respostas = json_decode($registro->Respostas, true);
                    
                    // Adiciona cada resposta ao array de itens
                    foreach ($respostas as $resposta) {
                        $item[] = $resposta['Resposta'];
                    }
                    
                    // Adiciona o item ao array de itens JSON
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

    public function visualizar($id){
        return view('Formularios.formulario',array(
           "Formulario" => json_decode(Formulario::find($id)->Formulario),
           'id' => $id,
           'submodulos'=> self::submodulos
        ));
    }

    public function responder(Request $request){
        try{
            $respostas = $request->all();
            $Form = json_decode(Formulario::find($respostas['IDForm'])->Formulario,true);
            $respondidas = [];
            unset($respostas['_token']);
            unset($respostas['IDForm']);
            foreach($respostas as $rKey =>$rVal){
                $Form[$rKey]['Resposta'] = $rVal;
            }
            foreach($Form as $f){
                array_push($respondidas,$f);
            }
            $Respostas = json_encode($respondidas);
            Resposta::create(array(
                "Respostas" => $Respostas,
                "IDForm" => $request->IDForm,
                "IDUser" => Auth::user()->id
            ));
            $rota = 'Formularios/Visualizar';
            $mensagem = "Sua Resposta foi Enviada por Email";
            $aid = $request->IDForm;
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDForm;
            $rota = 'Formularios/Visualizar';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function save(Request $request){
        $data = $request->all();
        $arrayFormParsed = [];
        $arrayForm = array_map(function($a){
            if(!empty($a['Conteudo'])){
                return $a;
            }
        },json_decode($data['Formulario'],true));

        foreach($arrayForm as $af){
            if(!is_null($af)){
                array_push($arrayFormParsed,$af);
            }
        }
        $data['Formulario'] = json_encode($arrayFormParsed);
        try{
            // $data = $request->all();
            if(!$request->id){
                //MailController::send($request->email,'Confirmação - Organizador','Mail.cadastroorganizador',array('Senha'=> $RandPW,'Email'=> $request->email));
                Formulario::create($data);
            }else{
                Formulario::find($request->id)->update($data);
            }
            $situacao['mensagem'] = "Salvo";
            $situacao['status'] = 'success';
        }catch(\Throwable $th){
            $situacao['mensagem'] = 'Erro '. $th->getMessage();
            $situacao['status'] = 'success';
        }finally{
            return json_encode($situacao);
        }
    }

    public function getFormularios(){
        $registros = DB::select("SELECT f.Titulo,e.Titulo as Evento,f.id as IDForm FROM formularios f INNER JOIN eventos e ON(e.id = f.IDEvento) ");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Evento;
                $item[] = "<a class='btn btn-danger btn-xs' href=".route('Formularios/Respostas/Export',$r->IDForm).">Exportar Respostas</a>&nbsp<a class='btn btn-success btn-xs' href=".route('Formularios/Edit',$r->IDForm).">Abrir</a>&nbsp<a class='btn btn-primary btn-xs' href=".route('Formularios/Visualizar',$r->IDForm).">Visualizar</a>&nbsp<a class='btn btn-secondary btn-xs' href=".route('Formularios/Respostas',$r->IDForm).">Respostas</a>";
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
