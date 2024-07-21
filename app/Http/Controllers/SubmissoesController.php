<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submissao;
use App\Models\Entrega;
use App\Models\Reprovacao;
use App\Models\User;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubmissoesController extends Controller
{
    public const submodulos = array([
        'nome' => 'Submissoes',
        'rota' => 'Submissoes/index',
        'endereco' => 'index'
    ]);

    public const cadastroSubmodulos = array([
        'nome' => 'Submissoes',
        'rota' => 'Submissoes/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Entregues',
        'rota' => 'Submissoes/Entregues',
        'endereco' => 'Entregues'
    ]);

    public function index(){
        $view = 'Submissoes.index';
        $data = [
            'submodulos' => self::submodulos,
            'id' => ''
        ];

        if(Auth::user()->tipo == 3){
            $view = 'Submissoes.indexInscrito';
            $data['Submissoes'] = DB::select("SELECT 
                s.Titulo,
                s.id,
                s.Regras
            FROM submissoes s
            INNER JOIN eventos e ON(s.IDEvento = e.id)
        ");
        }
        return view($view,$data);
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            'avaliadores' => User::where('tipo',2)->get(),
            'eventos' => Evento::all()
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Submissao::find($id);
            $view['submodulos'] = self::cadastroSubmodulos;
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

    public function entrega($IDSubmissao){
        $Submissao = Submissao::find($IDSubmissao);
        $IDUser = Auth::user()->id;
        $Entregas = DB::select("SELECT 
                MIN(e.created_at) as created_at,  -- Assume the earliest created_at for each group
                e.Titulo,
                MIN(e.id) as id,  -- Assume the minimum id for each group
                COALESCE(MAX(r.Status), 'Desconhecido') as Situacao,  -- Use MAX to get a representative status
                COALESCE(MAX(CASE WHEN e.id = r.IDEntrega THEN r.Feedback ELSE 'Aguardando Correção' END), 'Aguardando Correção') as Feedback
            FROM 
                entergas e
            LEFT JOIN 
                reprovacoes r ON(e.id = r.IDEntrega)
            WHERE 
                e.IDSubmissao = $IDSubmissao 
                AND e.IDInscrito = $IDUser 
            GROUP BY 
                e.Titulo,r.Status;
        ");
        return view('Submissoes.entrega',[
            'Submissao' => $Submissao,
            'Entregas' => $Entregas,
            'IDSubmissao' => $IDSubmissao
        ]);
    }

    public function correcao($IDEntrega){
        return view('Submissoes.correcao',[
            'submodulos' => self::submodulos,
            'Trabalho' => DB::select("SELECT 
                    e.*,
                    r.Status as Situacao,
                    CASE WHEN e.id = r.IDEntrega THEN r.Feedback ELSE 'Aguardando Correção' END as Feedback
                FROM entergas e
                LEFT JOIN reprovacoes r ON(e.id = r.IDEntrega)
                WHERE e.id = $IDEntrega ORDER BY r.id DESC LIMIT 1
            ")[0] 
        ]);
    }

    public function getTrabalho($IDEntrega){
        return json_encode(Entrega::find($IDEntrega));
    }

    public function corrigir(Request $request){
        try{
            Reprovacao::create($request->all());
            $mensagem = 'Trabalho corrigido com sucesso!';
            $rota = 'Submissoes/index';
            $aid = '';
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $rota = 'Submissoes/index';
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function entregues($IDSubmissao){
        return view('Submissoes.entregues',[
            'IDSubmissao' => $IDSubmissao,
            'submodulos' => self::cadastroSubmodulos
        ]);
    }

    public function saveEntrega(Request $request){
        try{
            $aid = $request->IDSubmissao;
            $rota = 'Submissoes/Entrega';
            $mensagem = 'Trabalho Enviado com Sucesso!';
            $status = 'success';
            $data = $request->all();
            $data['IDInscrito'] = Auth::user()->id;
            if(!$request->IDEntrega){
                Entrega::create($data);
            }else{
                unset($data['_token']);
                unset($data['_method']);
                Entrega::find($request->IDEntrega)->update($data);
            }
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $rota = 'Submissoes/Entrega';
            $aid = $request->IDSubmissao;
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function getSubmissoes(){
        if(Auth::user()->tipo == 1){
            $registros = DB::select("SELECT 
                e.Titulo as Evento,
                s.Categoria,
                s.Titulo,
                s.id,
                s.Regras
                FROM submissoes s
                INNER JOIN eventos e ON(s.IDEvento = e.id)
            ");
            if(count($registros) > 0){
                foreach($registros as $r){
                    $item = [];
                    $item[] = $r->Evento;
                    $item[] = $r->Categoria;
                    $item[] = $r->Titulo;
                    $item[] = $r->Regras;
                    $item[] = "<a href=".route('Submissoes/Edit',$r->id).">Abrir</a>";
                    $itensJSON[] = $item;
                }
            }else{
                $itensJSON = [];
            }
        }else{
            $IDAvaliador = Auth::user()->id;
            $registros = DB::select("SELECT ev.Titulo as Evento,
                    e.Titulo as Trabalho,
                    i.name as Inscrito,
                    s.Categoria,
                    e.id as IDEntrega
                FROM submissoes as s
                INNER JOIN entergas e ON(s.id = e.IDSubmissao)
                INNER JOIN users i ON(i.id = e.IDInscrito)
                INNER JOIN eventos ev ON(ev.id = s.IDEvento)
                WHERE e.IDAvaliador = $IDAvaliador
            ");
            if(count($registros) > 0){
                foreach($registros as $r){
                    $item = [];
                    $item[] = $r->Evento;
                    $item[] = $r->Inscrito;
                    $item[] = $r->Trabalho;
                    $item[] = $r->Categoria;
                    $item[] = "<a href=".route('Submissoes/Correcao',$r->IDEntrega).">Abrir</a>";
                    $itensJSON[] = $item;
                }
            }else{
                $itensJSON = [];
            }
        }
        
        $resultados = [
            "recordsTotal" => intval(count($registros)),
            "recordsFiltered" => intval(count($registros)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function setAvaliador(Request $request){
        try{
            $Avaliadores = [];
            $Correcoes = [];
            //
            foreach($request->IDAvaliador as $av){
                if(!is_null($av)){
                    array_push($Avaliadores,$av);
                }
            }
            //
            for($i=0;$i<count($Avaliadores);$i++){
                $Correcoes[] = array(
                    "IDAvaliador" => $Avaliadores[$i],
                    "IDInscrito" => $request->Inscrito[$i]
                );
            }
            //
            foreach($Correcoes as $c){
                Entrega::where('IDInscrito',$c['IDInscrito'])->update(["IDAvaliador"=>$c['IDAvaliador']]);
            }
            //
            $aid = $request->IDSubmissao;
            $rota = 'Submissoes/Entregues';
            $mensagem = 'Trabalhos Distribuidos com Sucesso!';
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $rota = 'Submissoes/Entregues';
            $aid = $request->IDSubmissao;
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
            //dd($Correcoes);
        }
    }

    public function getEntregues($IDSubmissao){
        
        $selectAvaliador = "<select name='IDAvaliador[]'>";
        $selectAvaliador .= "<option value=''>Selecione</option>";
        foreach(User::select('id','name')->where('tipo',2)->get() as $a){
            $selectAvaliador .="<option value='$a->id'>".$a->name."</option>";
        }
        $selectAvaliador .= "</select>";

        $SQL = "SELECT
                e.Titulo,
                s.Categoria,
                a.id as IDAvaliador,
                u.name as Inscrito,
                a.name as Avaliador,
                r.Status as Situacao,
                e.IDInscrito
            FROM entergas e
            INNER JOIN submissoes s ON(s.id = e.IDSubmissao)
            INNER JOIN users u ON(u.id = e.IDInscrito)
            LEFT JOIN users a ON(a.id = e.IDAvaliador)
            LEFT JOIN reprovacoes r ON(e.id = r.IDEntrega) WHERE s.id = $IDSubmissao";
        
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Categoria;
                $item[] = $r->Inscrito;
                $item[] = ($r->IDAvaliador == 0) ? $selectAvaliador."<input type='hidden' value='$r->IDInscrito' name='Inscrito[]'>" : $r->Avaliador;
                $item[] = $r->Situacao;
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
