<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submissao;
use App\Models\Entrega;
use Illuminate\Support\Facades\Session;
use App\Models\Reprovacao;
use App\Models\User;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Storage;

class SubmissoesController extends Controller
{
    public const submodulos = array([
        'nome' => 'Submissoes',
        'rota' => 'Submissoes/index',
        'endereco' => 'index'
    ]);

    public const cadastroSubmodulos = array([
        'nome' => 'Submissoes',
        'rota' => 'Submissoes/Edit',
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
            $AND = '';
            $currentId = Auth::user()->id;
            if(Session::has('IDEvento')){
                $AND = ' WHERE s.IDEvento='.Session::get('IDEvento');
                
            }
            //dd($currentId);
            if(Entrega::where('IDInscrito',$currentId)->first()){
                $AND .= " AND s.id IN(SELECT IDSubmissao FROM entergas WHERE entergas.IDInscrito = $currentId)";
            }

            $data['Submissoes'] = DB::select("
                SELECT 
                    s.Categoria,
                    s.id,
                    s.Regras,
                    CASE WHEN s.id = en.IDSubmissao THEN en.IDEntrega ELSE 0 END as IDEntrega
                FROM 
                    submissoes s
                LEFT JOIN (
                    SELECT 
                        en.IDSubmissao, 
                        en.id as IDEntrega 
                    FROM 
                        entergas en
                    WHERE 
                        en.IDInscrito = $currentId
                ) as en ON s.id = en.IDSubmissao
                INNER JOIN 
                    eventos e ON s.IDEvento = e.id 
                $AND
                GROUP BY 
                    s.Categoria, 
                    s.id, 
                    s.Regras, 
                    en.IDEntrega
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
            $data = $request->all();
            if(!$request->id){
                $rota = 'Submissoes/Novo';
                $aid = '';
                if($request->file('Regras')){
                    $Regras = $request->file('Regras')->getClientOriginalName();
                    $request->file('Regras')->storeAs('regras_submissao',$Regras,'public');
                    $data['Regras'] = $Regras;
                }
                Submissao::create($data);
            }else{
                if($request->file('Regras')){
                    $Regras = $request->file('Regras')->getClientOriginalName();
                    Storage::disk('public')->delete('regras_submissao/'.$request->oldRegras);
                    $request->file('Regras')->storeAs('regras_submissao',$Regras,'public');
                    $data['Regras'] = $Regras;
                }
                $rota = 'Submissoes/Edit';
                $aid = $request->id;
                Submissao::find($request->id)->update($data);
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

    public function entrega($IDSubmissao,$IDEntrega){
        $Submissao = Submissao::find($IDSubmissao);
        $Evento = Evento::find($Submissao->IDEvento);
        $IDUser = Auth::user()->id;
        $AND = ' e.IDSubmissao='.$IDSubmissao;
        $SEL = 'MAX(e.Apresentador) as Apresentador,';
        if(Auth::user()->tipo == 3){
            $AND .=  " AND e.IDInscrito = $IDUser";
            $SEL = '';
        }

        if($IDEntrega > 0){
            $AND .= " AND e.id =".$IDEntrega;
        }

        $SQL = "SELECT 
            MIN(e.created_at) as created_at,  -- Assume the earliest created_at for each group
            e.Titulo,
            MIN(e.Autores) as Autores,
            MIN(e.IDSubmissao) as IDSubmissao,
            MIN(e.palavrasChave) as palavrasChave,
            MIN(e.Tematica) as Tematica,
            MIN(e.Descricao) as Descricao,
            $SEL
            MIN(e.id) as id,  -- Assume the minimum id for each group
            COALESCE(MAX(r.Status), 'Desconhecido') as Situacao,  -- Use MAX to get a representative status
            COALESCE(MAX(CASE WHEN e.id = r.IDEntrega THEN r.Feedback ELSE 'Aguardando Correção' END), 'Aguardando Correção') as Feedback,
            MAX(r.id) as last_repro_id  -- Captura o maior ID de reprovação
        FROM 
            entergas e
        LEFT JOIN 
            reprovacoes r ON(e.id = r.IDEntrega)
        WHERE  
            $AND
        GROUP BY 
            e.Titulo, r.Status
        ORDER BY 
            last_repro_id DESC;  -- Ordena pelo maior ID de reprovação

        ";
        $Entregas = DB::select($SQL);
        //dd($Entregas);
        return view('Submissoes.entrega',[
            'Submissao' => $Submissao,
            'Entrega' => (session('Submissao')) ? session('Submissao') : '',
            'Evento' => $Evento,
            'Entregas' => $Entregas,
            'IDSubmissao' => $IDSubmissao,
            'IDEntrega' => ($IDEntrega > 0) ? $IDEntrega : 0,
            'debug' => $SQL,
            'Tematica' => [
                'FACALE',
                'FACE',
                'FACET',
                'FADIR',
                'FAED',
                'FAEN',
                'FAIND',
                'FCA',
                'FCBA',
                'FCH',
                'FCS',
                'Outro'
            ]
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
            
            $submissao = Submissao::find($request->IDSubmissao);

            $data = $request->all();
            $texto = explode(" ",$request->Descricao);
            $palavras = [];
            $conjuncoes = [
                // Conjunções coordenativas
                "e", "mas", "ou", "porém", "contudo", "todavia", "no entanto", "portanto", "pois", "logo", "porque", "nem", "tampouco", "quer", "sequer", "assim", "então",
                "que", "se", "como", "quando", "enquanto", "conquanto", "embora", "para que", "a fim de que", "desde que", "contanto que", "caso", "se bem que", "por mais que", "apesar de que",
                "a", "ante", "após", "até", "com", "contra", "de", "desde", "em", "entre", "para", "perante", "por", "sem", "sob", "sobre", "trás","o", "a", "os", "as", "um", "uma", "uns", "umas","ele",
                "ela", "eles", "elas", "me", "te", "lhe", "nos", "vos", "se", "este", "esse", "aquele", "esta", "essa", "aquela", "isto", "isso", "aquilo", "que", "quem", "qual", "cujo",
                "não", "sim", "nunca", "sempre", "talvez", "aqui", "ali", "lá", "hoje", "ontem", "amanhã", "já", "depois", "mais", "menos", "muito", "pouco", "bem", "mal",
                "de", "que", "se", "me", "te", "nos", "vos", "lhe", "já", "ainda", "mesmo", "também", "além", "tal", "cá", "lá", "tão", "quanto", "quer", "nem", "mesmo", "assim",
            ];

            foreach($texto as $t){
                if(!in_array($t,$conjuncoes)){
                    array_push($palavras,$t);
                }
            }

            if(count($palavras) > $submissao->MaxLength || count($palavras) < $submissao->MinLength){
                $rota = 'Submissoes/Entrega';
                $aid = ["IDSubmissao"=>$request->IDSubmissao,"IDEntrega" => 0];
                session()->flash('Submissao',$request->all());
                $mensagem = "O Trabalho Submetido não atende as Exigências Estabelecida na Norma!";
                $status = 'error';
                return false;
            }

            $status = 'success';
            if(Auth::user()->tipo == 3){
                $data['IDInscrito'] = Auth::user()->id;
                $rota = 'Submissoes/Entrega';
                $mensagem = 'Trabalho Enviado com Sucesso!';
                $aid = ["IDSubmissao"=>$request->IDSubmissao,"IDEntrega" => 0];
                if(!$request->IDEntrega){
                    Entrega::create($data);
                }else{
                    unset($data['_token']);
                    unset($data['_method']);
                    Entrega::find($request->IDEntrega)->update($data);
                }
            }else{
                $rota = 'Submissoes/index';
                $mensagem = 'Trabalho Atualizado com Sucesso!';
                $aid = ["IDSubmissao"=>$request->IDSubmissao,"IDEntrega" => $request->IDEntrega];
                unset($data['_token']);
                unset($data['_method']);
                Entrega::find($request->IDEntrega)->update($data);
            }
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            if(Auth::user()->tipo == 3){
                $rota = 'Submissoes/Entrega';
                $aid = ["IDSubmissao"=>$request->IDSubmissao,"IDEntrega" => 0];
            }else{
                $rota = 'Submissoes/index';
                $aid = ["IDSubmissao"=>$request->IDSubmissao,"IDEntrega" => $request->IDEntrega];
            }
            
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
                    $item[] = "<a href='" . url('storage/regras_submissao/' . $r->Regras) . "' target='_blank'>".$r->Regras."</a>";
                    $item[] = "<a href=".route('Submissoes/Edit',$r->id).">Abrir</a>";
                    $itensJSON[] = $item;
                }
            }else{
                $itensJSON = [];
            }
        }else{
            $IDAvaliador = Auth::user()->id;
            $registros = DB::select("SELECT 
                    ev.Titulo as Evento,
                    i.name as Inscrito,
                    s.Categoria,
                    s.Regras,
                    e.Titulo,
                    e.id as IDEntrega,
                    r.Status
                FROM 
                    submissoes as s
                INNER JOIN 
                    entergas e ON s.id = e.IDSubmissao
                INNER JOIN 
                    users i ON i.id = e.IDInscrito
                INNER JOIN 
                    eventos ev ON ev.id = s.IDEvento
                LEFT JOIN 
                    reprovacoes r ON r.id = (
                        SELECT 
                            r2.id 
                        FROM 
                            reprovacoes r2 
                        WHERE 
                            r2.IDEntrega = e.id 
                        ORDER BY 
                            r2.id DESC 
                        LIMIT 1
                    )
                WHERE 
                    e.IDAvaliador = $IDAvaliador
                ORDER BY 
                    e.id;
            ");
            if(count($registros) > 0){
                foreach($registros as $r){
                    $item = [];
                    $item[] = $r->Evento;
                    $item[] = $r->Status;
                    $item[] = $r->Titulo;
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
                //if(!is_null($av)){
                    array_push($Avaliadores,$av);
                //}
            }
            //
            for($i=0;$i<count($Avaliadores);$i++){
                $Correcoes[] = array(
                    "IDAvaliador" => $Avaliadores[$i],
                    "IDInscrito" => $request->Inscrito[$i]
                );
            }
            //dd($Correcoes);
            //
            foreach($Correcoes as $c){
                if(!is_null($c['IDAvaliador'])){
                    Entrega::where('IDInscrito',$c['IDInscrito'])->update(["IDAvaliador"=>$c['IDAvaliador']]);
                }
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

    public function removeAtr($IDEntrega){
        Entrega::find($IDEntrega)->update(["IDAvaliador"=>0]);
    }

    public function getEntregues($IDSubmissao){
    

        $selectAvaliador = "<select name='IDAvaliador[]'>";
        $selectAvaliador .= "<option value=''>Selecione</option>";
        foreach(User::select('id','name')->where('tipo',2)->get() as $a){
            $selectAvaliador .="<option value='$a->id'>".$a->name."</option>";
        }
        $selectAvaliador .= "</select>";

        $WHERE = "";
        if(isset($_GET['Modalidade']) && !empty($_GET['Modalidade'])){
            $WHERE = " AND s.Categoria='".$_GET['Modalidade']."'";
        }

        $WHERE = "";
        if (isset($_GET['Modalidade']) && !empty($_GET['Modalidade'])) {
            $WHERE = " AND s.Categoria='" . $_GET['Modalidade'] . "'";
        }

        $SQL = "SELECT
            e.Titulo,
            s.Categoria,
            a.id as IDAvaliador,
            u.name as Inscrito,
            e.Apresentador as Apresentador,
            a.name as Avaliador,
            COALESCE(r.Status, 'Aguardando Correção') as Status,
            e.IDInscrito,
            e.id as IDEntrega,
            s.id as IDSubmissao
        FROM entergas e
        INNER JOIN submissoes s ON (s.id = e.IDSubmissao)
        INNER JOIN users u ON (u.id = e.IDInscrito)
        LEFT JOIN users a ON (a.id = e.IDAvaliador)
        LEFT JOIN (
            SELECT r1.IDEntrega, r1.Status
            FROM reprovacoes r1
            INNER JOIN (
                SELECT IDEntrega, MAX(id) as MaxID
                FROM reprovacoes
                GROUP BY IDEntrega
            ) r2 ON r1.id = r2.MaxID
        ) r ON (e.id = r.IDEntrega)
        WHERE s.id = $IDSubmissao $WHERE";

            //dd($SQL);
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $RemoveATR = '"'. strval(route('Submissoes/RemoveAtr',$r->IDEntrega)). '"';
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Categoria;
                $item[] = $r->Inscrito;
                $item[] = $r->Apresentador;
                $item[] = ($r->IDAvaliador == 0) ? $selectAvaliador."<input type='hidden' value='$r->IDInscrito' name='Inscrito[]'>" : $r->Avaliador." <button class='btn btn-xs btn-danger' type='button' onclick='removerAtribuicao($RemoveATR)'>Remover Atribuição</button>";
                $item[] = $r->Status;
                $item[] = "<a href=".route('Submissoes/Entrega',["IDEntrega"=>$r->IDEntrega,"IDSubmissao"=>$r->IDSubmissao]).">Abrir</a> <a href=".route('Submissoes/Correcao',$r->IDEntrega).">Corrigir</a>";
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
