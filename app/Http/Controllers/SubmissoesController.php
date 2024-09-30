<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submissao;
use App\Models\Entrega;
use Illuminate\Support\Facades\Session;
use App\Models\Apresentacao;
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
    ],[
        'nome'=> 'Apresentacoes',
        'rota'=>'Submissoes/Apresentacoes',
        'endereco'=> 'Apresentacoes'
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
                $IDEvento = Session::get('IDEvento');
                
            }
            //dd($currentId);
            $Entrega = DB::select("SELECT s.id FROM submissoes s INNER JOIN entergas en ON(s.id = en.IDSubmissao) WHERE s.IDEvento = $IDEvento AND en.IDInscrito = $currentId");
            if($Entrega){
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

    public function apresentacoes(){
        return view('Submissoes.apresentacoes',[
            'submodulos'=> self::submodulos
        ]);
    }

    public function cadastro($id = null){
        $Evento = Evento::find(Session::get('IDEvento'));
        $Modalidades = [];
        foreach(json_decode($Evento->Modalidades,true) as $m){
            if(!Submissao::where('IDEvento',Session::get('IDEvento'))->where('Categoria',$m)->exists()){
                array_push($Modalidades,$m);
            }
        }
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            'avaliadores' => User::where('tipo',2)->get(),
            'Modalidades' => $Modalidades
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Submissao::find($id);
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Modalidades'] = json_decode($Evento->Modalidades,true);
        }

        return view('Submissoes.cadastro', $view);
    }

    public function delete(Request $request){
        return Submissao::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $IDEvento = Session::get('IDEvento');
            $data['IDEvento'] = $IDEvento;
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
                if(Submissao::where('IDEvento',Session::get('IDEvento'))->where('id','!=',$request->id)->where('Categoria',$request->Categoria)->exists()){
                    $mensagem = 'Já Existe uma Submissão com Essa Modalidade!';
                    $status = 'error';
                    $rota = 'Submissoes/Edit';
                    $aid = $request->id;
                    return false;
                }
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
        if(Auth::user()->tipo == 3){
            $AND .=  " AND e.IDInscrito = $IDUser";
        }

        if($IDEntrega > 0){
            $AND .= " AND e.id =".$IDEntrega;
        }

        $SQL = "SELECT e.Titulo,
            e.id as IDEntrega,
            s.id as IDSubmissao,
            i.name as Inscrito,
            e.Apresentador,
            e.IDAvaliador,
            e.IDInscrito,
            e.Autores,
            e.palavrasChave,
            e.Tematica,
            e.Feedback,
            e.Descricao,
            e.Status,
            a.name as Avaliador,
            e.Status
        FROM entergas e
        INNER JOIN submissoes s ON(s.id = e.IDSubmissao)
        INNER JOIN users i ON (i.id = e.IDInscrito)
        LEFT JOIN users a ON(a.id = e.IDAvaliador)
        WHERE $AND
        ";
        //dd($SQL);
        $Entregas = DB::select($SQL);
        if(count($Entregas) > 0){
            if(Auth::user()->tipo == 1){
                $Status = $Entregas['Status'];
            }else{
                $Status = $Entregas[0]->Status;
            }
        }else{
            $Status = "Aguardando Correção";
        }
        //dd($Entregas);
        return view('Submissoes.entrega',[
            'Submissao' => $Submissao,
            'Status' => $Status,
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
            'Trabalho' => Entrega::find($IDEntrega),
            "Submissoes"=> Submissao::select('Categoria','id')->where('IDEvento',Session::get('IDEvento'))->get()
        ]);
    }

    public function getTrabalho($IDEntrega){
        return json_encode(Entrega::find($IDEntrega));
    }

    public function corrigir(Request $request){
        try{
            Entrega::find($request->IDEntrega)->update([
                "Status"=> $request->Status,
                "Feedback"=>$request->Feedback,
                "IDSubmissao"=>$request->IDSubmissao
            ]);
            $IDAluno = Entrega::find($request->IDEntrega)->IDInscrito;
            //MailController::send(User::find($IDAluno)->email,'Aviso de Correção da Submissão','Mail.submissao',array('Status'=> $request->Status,'Mensagem'=> "Sua Submissão foi Corrigida!"));
            $mensagem = 'Trabalho corrigido com sucesso!';
            $rota = 'Submissoes/Correcao';
            $aid = $request->IDEntrega;
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
                $rota = (Auth::user()->tipo == 3) ? 'Submissoes/Entrega' : 'Submissoes/Trabalho';
                $aid = ["IDSubmissao"=>$request->IDSubmissao,"IDEntrega" => (Auth::user()->tipo == 3) ? 0 : $request->IDEntrega ];
                session()->flash('Submissao',$request->all());
                $mensagem = "O Trabalho Submetido não atende as Exigências Estabelecida na Norma!";
                $status = 'error';
                return false;
            }else{
                $status = 'success';
                if(Auth::user()->tipo == 3){
                    $data['IDInscrito'] = Auth::user()->id;
                    //MailController::send(Auth::user()->email,'Aviso de Submissão','Mail.submissao',array('Status'=> "Aguardando Correção",'Mensagem'=> "Por favor Aguarde sua Submissão ser Corrigida"));
                    $rota = 'Submissoes/Entrega';
                    $mensagem = 'Trabalho Enviado com Sucesso!';
                    $aid = ["IDSubmissao"=>$request->IDSubmissao,"IDEntrega" => 0];
                    $data['Status'] = "Aguardando Correção";
                    if(!$request->IDEntrega){
                        $data['NEntrega'] = rand(1,99999);
                        Entrega::create($data);
                    }else{
                        unset($data['_token']);
                        unset($data['_method']);
                        Entrega::find($request->IDEntrega)->update($data);
                    }
                }else{
                    $rota = 'Submissoes/Trabalho';
                    $aid = $request->IDEntrega;
                    $mensagem = "Trabalho foi Alterado com Sucesso!";
                    unset($data['_token']);
                    unset($data['_method']);
                    Entrega::find($request->IDEntrega)->update($data);
                }
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

    public function getTrabalhoOrganizador($IDTrabalho){
        $Trabalho = Entrega::find($IDTrabalho);
        $Submissao = Submissao::find($Trabalho->IDSubmissao);
        return view('Submissoes.entrega',[
            "Entregas"=> $Trabalho,
            "Evento" => Evento::find(Session::get('IDEvento')),
            "IDSubmissao"=> $Trabalho->IDSubmissao,
            "IDEntrega"=>$IDTrabalho,
            "Submissao"=> $Submissao,
            'Status'=> $Trabalho->Status,
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

    public function getSubmissoes(){
        $IDEvento = Session::get('IDEvento');
        if(Auth::user()->tipo == 1){
            $registros = DB::select("SELECT 
                e.Titulo as Evento,
                s.Categoria,
                s.id,
                s.Regras
                FROM submissoes s
                INNER JOIN eventos e ON(s.IDEvento = e.id) AND s.IDEvento = $IDEvento
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
            $SQL = "SELECT e.Titulo,
                e.id as IDEntrega,
                s.id as IDSubmissao,
                i.name as Inscrito,
                e.Apresentador,
                e.IDAvaliador,
                e.IDInscrito,
                a.name as Avaliador,
                ev.Titulo as Evento,
                s.Categoria,
                e.Status
            FROM entergas e
            INNER JOIN submissoes s ON(s.id = e.IDSubmissao)
            INNER JOIN eventos ev ON(ev.id = s.IDEvento)
            INNER JOIN users i ON (i.id = e.IDInscrito)
            LEFT JOIN users a ON(a.id = e.IDAvaliador)
            WHERE a.id = $IDAvaliador AND ev.id IN(SELECT IDEvento FROM bancaevento WHERE bancaevento.IDUser = $IDAvaliador)";
            $registros = DB::select($SQL);
            if(count($registros) > 0){
                foreach($registros as $r){
                    $item = [];
                    $item[] = $r->Evento;
                    $item[] = empty($r->Status) ? 'Aguardando Correção' : $r->Status;
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

    public function apresentacoesList(){
        $SQL = "SELECT e.Titulo,
            e.id as IDEntrega,
            e.Autores,
            e.Descricao,
            e.Apresentador,
            CASE WHEN ap.Apresentou = 0 THEN 0 ELSE 1 END as Apresentou
        FROM entergas e
        INNER JOIN submissoes s ON(s.id = e.IDSubmissao)
        INNER JOIN users i ON (i.id = e.IDInscrito)
        INNER JOIN apresentacoes ap ON(e.id = ap.IDEntrega)
        WHERE e.Status = 'Aprovado'";
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $apst = '';
                if($r->Apresentou == 1){
                    $apst = 'checked';
                }
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Autores;
                $item[] = $r->Descricao;
                $item[] = $r->Apresentador;
                $item[] = "<input type='checkbox' name='Apresentou[]' $apst value='$r->IDEntrega'>";
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

    public function saveApresentacoes(Request $request){
        try{
            Apresentacao::where('IDAtividade','>',0)->update(['Apresentou'=>0]);
            foreach($request->Apresentou as $a){
                Apresentacao::where('IDEntrega',$a)->update(['Apresentou'=>1]);
            }
            $rota = 'Submissoes/Apresentacoes';
            $mensagem = "Presença Concluida";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $rota = 'Submissoes/Apresentacoes';
            $status = 'error';
        }finally{
            return redirect()->route($rota)->with($status, $mensagem);
        }
    }

    public function getEntregues($IDSubmissao){
        $IDEvento = Session::get('IDEvento');
        $Aval = DB::select("SELECT users.id,name FROM users INNER JOIN bancaevento ON(bancaevento.IDUser = users.id) WHERE bancaevento.IDEvento = $IDEvento ");
        $selectAvaliador = "<select name='IDAvaliador[]'>";
        $selectAvaliador .= "<option value=''>Selecione</option>";
        foreach($Aval as $a){
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

        $SQL = "SELECT e.Titulo,
            e.id as IDEntrega,
            s.id as IDSubmissao,
            i.name as Inscrito,
            e.Autores,
            e.palavrasChave,
            e.Tematica,
            e.Apresentador,
            e.IDAvaliador,
            e.NEntrega,
            e.IDInscrito,
            a.name as Avaliador,
            e.Status
        FROM entergas e
        INNER JOIN submissoes s ON(s.id = e.IDSubmissao)
        INNER JOIN users i ON (i.id = e.IDInscrito)
        LEFT JOIN users a ON(a.id = e.IDAvaliador)
        WHERE s.id = $IDSubmissao
        $WHERE
        ";

            //dd($SQL);
        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $RemoveATR = '"'. strval(route('Submissoes/RemoveAtr',$r->IDEntrega)). '"';
                $item = [];
                $item[] = $r->NEntrega;
                $item[] = $r->Titulo;
                $item[] = $r->Inscrito;
                $item[] = $r->Apresentador;
                $item[] = $r->palavrasChave;
                $item[] = $r->Tematica;
                $item[] = ($r->IDAvaliador == 0) ? $selectAvaliador."<input type='hidden' value='$r->IDInscrito' name='Inscrito[]'>" : $r->Avaliador." <button class='btn btn-xs btn-danger' type='button' onclick='removerAtribuicao($RemoveATR)'>Remover Atribuição</button>";
                $item[] = empty($r->Status) ? 'Aguardando Correção' : $r->Status;
                $item[] = "<a href=".route('Submissoes/Correcao',$r->IDEntrega).">Abrir</a> <a href=".route('Submissoes/Trabalho',$r->IDEntrega).">Editar Trabalho</a>";
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
