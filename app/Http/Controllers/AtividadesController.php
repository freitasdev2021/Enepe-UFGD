<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ZoomController;
use App\Models\Atividade;
use App\Models\Apresentacao;
use App\Models\Evento;
use App\Models\Sala;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DyteController;
class AtividadesController extends Controller
{
    public const submodulos = array([
        'nome' => 'Apresentações',
        'rota' => 'Atividades/index',
        'endereco' => 'index'
    ]);

    public function index(){
        $IDEvento = Session::get('IDEvento');
        if(Auth::user()->tipo == 1){
            return view('Atividades.index', [
                'submodulos' => self::submodulos,
                'IDEvento' => $IDEvento
            ]);
        }else{
            $AND = '';
            if(Session::has('IDEvento')){
                $AND = ' WHERE a.IDEvento='.Session::get('IDEvento');
            }
    
            if(isset($_GET['Apresentacoes']) && $_GET['Apresentacoes'] == "Minhas"){
                $AND .= " AND en.IDInscrito=".Auth::user()->id;
            }
    
            $SQL = <<<SQL
                SELECT 
                    a.id,
                    a.Titulo,
                    a.Inicio,
                    (
                        SELECT
                            CONCAT(
                                '[',
                                GROUP_CONCAT(
                                    '{'
                                    ,'"titulo":"', en2.Titulo, '"'
                                    ,',"apresentador":"', en2.Apresentador, '"'
                                    ,'}'
                                    SEPARATOR ','
                                ),
                                ']'
                            )
                        FROM 
                            apresentacoes ap2
                        INNER JOIN 
                            entergas en2 ON(ap2.IDEntrega = en2.id) 
                        WHERE 
                            ap2.IDAtividade = a.id
                    ) AS listaApresentacoes
                FROM 
                    atividades a 
                LEFT JOIN apresentacoes ap ON(a.id = ap.IDAtividade)
                INNER JOIN entergas en ON(en.id = ap.IDEntrega)
                $AND
                GROUP BY a.id, a.Titulo, a.Inicio
            SQL;

    
            return view('Atividades.indexInscrito',[
                'Atividades' => DB::select($SQL)
            ]);
        }
    }

    public function cadastro($id = null){
        $WHERE = "";
        if(isset($_GET['Modalidade']) && !empty($_GET['Modalidade'])){
            $WHERE = " AND s.Categoria='".$_GET['Modalidade']."'";
        }

        if($id){
            $WHERE .= " AND e.id NOT IN(SELECT ap.IDEntrega FROM apresentacoes ap WHERE ap.IDAtividade != $id)";
        }else{
            $WHERE .=" AND e.id NOT IN(SELECT ap.IDEntrega FROM apresentacoes ap)";
        }

        $IDEvento = Session::get('IDEvento');
        $SQL = "SELECT e.Titulo,
            e.id as IDEntrega,
            e.Autores,
            e.Descricao,
            e.Apresentador
        FROM entergas e
        INNER JOIN submissoes s ON(s.id = e.IDSubmissao)
        INNER JOIN users i ON (i.id = e.IDInscrito)
        WHERE e.Status = 'Aprovado' $WHERE
        ";

        $view = array(
            'id' => '',
            'IDEvento' => $IDEvento,
            'submodulos' => self::submodulos,
            "Aprovados" => DB::select($SQL),
            "Modalidades"=>json_decode(Evento::find($IDEvento)->Modalidades),
            "CurrentRoute" => route('Atividades/Novo')
        );

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::submodulos;
            $view['Registro'] = Atividade::find($id);
            $view['CurrentRoute'] = route('Atividades/Edit',$id);
            $view['Apresentadores'] = Apresentacao::where('IDAtividade',$id)->pluck('IDEntrega')->toArray();
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
            $data['IDEvento'] = Session::get('IDEvento');
            if(!$request->id){

                //dd($accessToken);
                ////
                $rota = 'Atividades/Novo';
                $aid = '';
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
                $Atividade = Atividade::create($data);
                foreach($data['Apresentar'] as $ap){
                    Apresentacao::create(array(
                        "IDEntrega" => $ap,
                        "IDAtividade"=> $Atividade->id,
                    ));
                }
            }else{
                $rota = 'Atividades/Edit';
                $aid = $request->id;
                if($request->mudarApresentacoes){
                    Apresentacao::where('IDAtividade',$request->id)->delete();
                    foreach($data['Apresentar'] as $ap){
                        Apresentacao::create(array(
                            "IDEntrega" => $ap,
                            "IDAtividade"=> $request->id,
                        ));
                    }
                }
                Atividade::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->id;
            $rota = 'Atividades/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
            //dd($request->IDEvento);
        }
    }

    public function getAtividades($IDEvento){
        $registros = DB::select("SELECT a.IDEvento,a.id,a.Titulo,a.Inicio FROM atividades a WHERE a.IDEvento = $IDEvento");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = Controller::data($r->Inicio,'d/m/Y H:i');
                $item[] = "<a href=".route('Atividades/Edit',$r->id)." class='btn bg-fr text-white btn-xs'>Abrir</a>
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
