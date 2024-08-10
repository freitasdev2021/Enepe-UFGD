<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\User;
use App\Http\Controllers\MailController;;
use App\Models\Inscricao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Storage;
class EventosController extends Controller
{
    public const submodulos = array([
        'nome' => 'Eventos',
        'rota' => 'Eventos/index',
        'endereco' => 'index'
    ]);

    public const cadastroSubmodulos = array([
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
    ],[
        'nome' => 'Inscrições',
        'rota' => 'Eventos/Inscricoes',
        'endereco' => 'Inscricoes'
    ]);

    public function index(){
        $data = [
            'submodulos' => self::submodulos,
            'id' => ''
        ];
        $view = 'Eventos.index';
        if(Auth::user()->tipo == 3){
            $view = 'Eventos.indexInscrito';
            $currentId = Auth::user()->id;
            $data['Eventos'] = DB::select("SELECT 
                    e.Titulo AS Evento,
                    MAX(e.Capa) as Capa,
                    MIN(e.id) AS IDEvento,  -- Usa MIN para obter o menor id do grupo
                    MAX(e.Descricao) AS Descricao,  -- Usa MAX para evitar problemas de agregação
                    MAX(CASE WHEN i.IDUser = $currentId THEN 1 ELSE 0 END) AS Inscrito,  -- Usa MAX para obter um valor representativo,
                    MAX(CASE WHEN e.TERInscricoes > NOW() THEN 'O prazo para a inscrição do evento está encerrado' ELSE 'Inscreva-se' END) as Inscricao
                FROM 
                    eventos e
                LEFT JOIN 
                    inscricoes i ON i.IDEvento = e.id
                WHERE 
                    e.Termino > NOW()
                GROUP BY 
                    e.Titulo                
            ");
        }
        return view($view,$data);
    }

    public function entrar(Request $request){
        try{
            if(!Inscricao::where('IDUser',Auth::user()->id)){
                $mensagem = 'Você não está inscrito nesse Evento!';
                $status = 'error';
                $rota = 'Eventos/index';
                return false;
            }
            Session::put('IDEvento',$request->IDEvento);
            $aid = '';
            $mensagem = '';
            $status = 'success';
            $rota = 'Palestras/index';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Eventos/incex';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function inscricoes($IDEvento){
        return view("Eventos.inscritos",[
            "IDEvento" => $IDEvento,
            "submodulos" => self::cadastroSubmodulos
        ]);
    }

    public function inscreverAluno($IDEvento,$IDAluno=null){
        $view = [
            "IDEvento" => $IDEvento,
            "submodulos" => self::cadastroSubmodulos
        ];

        if($IDAluno){
            $SQL = <<<SQL
                SELECT
                    u.name as Nome,
                    i.Categoria,
                    i.id as IDInscricao,
                    u.email as Email,
                    u.id as IDUser
                FROM inscricoes i
                INNER JOIN users u ON(u.id = i.IDUser)
                WHERE i.IDEvento = $IDEvento AND i.IDUser = $IDAluno
            SQL;
            $view['Registro'] = DB::select($SQL)[0];
        }
        return view('Eventos.inscreverAluno',$view);
    }

    public function saveInscricaoAluno(Request $request){
        try{
            if(!$request->id){
                $RandPW = rand(100000,999999);
                $dataUser = array(
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => Hash::make($RandPW),
                    'tipo' => 3
                );
                $setUser = User::create($dataUser);
                Inscricao::create([
                    'IDUser' => $setUser->id,
                    'IDEvento' => $request->IDEvento,
                    'Categoria' => $request->Categoria
                ]);
                // Enviar e-mail de confirmação com a senha
                MailController::send($request->email,'Confirmação de Inscrição pela Universidade','Mail.inscrito',array('Senha'=> $RandPW,'Email'=>$request->email));
                $mensagem = 'Inscrição Concluida! O Comprovante e os dados de Acesso a Plataforma serão enviados via Email';
                $aid = $request->IDEvento;
                $rota = 'Eventos/Inscricoes/inscreverAluno';
            }else{
                //ALTERAÇÃO NORMAL
                //dd($request->all());
                $userUpdate = ["name"=>$request->name,"email"=>$request->email];
                $mailUpdate = array('Senha'=> "a Mesma",'Email'=>$request->email);
                
                //CASO ALTEREM A SENHA
                if($request->alteraSenha){
                    $RandPW = rand(100000,999999);
                    $userUpdate['password'] = Hash::make($RandPW);
                    $mailUpdate['Senha'] = $RandPW;
                }
                Inscricao::where('IDUser',$request->id)->update(["Categoria"=>$request->Categoria]);
                User::find($request->id)->update($userUpdate);
               
                // Enviar e-mail de confirmação com a senha
                MailController::send($request->email,'Confirmação de Inscrição pela Universidade','Mail.inscrito',$mailUpdate);
                $mensagem = 'Inscrição Alterada! O Comprovante e os dados de Acesso a Plataforma serão enviados via Email';
                $rota = 'Eventos/Inscricoes/editarAluno';
                $aid = array('IDEvento'=> $request->IDEvento,"IDAluno"=>$request->id);
            }
            
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDEvento;
            $rota = 'Eventos/Inscricoes/inscreverAluno';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function excluirInscricao(Request $request){

    }

    public function getInscricoes($IDEvento){
        $SQL = <<<SQL
            SELECT
                u.name as Nome,
                i.Categoria,
                i.id as IDInscricao,
                u.email as Email,
                u.id as IDUser
            FROM inscricoes i
            INNER JOIN users u ON(u.id = i.IDUser)
            WHERE i.IDEvento = $IDEvento
        SQL;
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Categoria;
                $item[] = $r->Email;
                $item[] = "<a href=".route('Eventos/Inscricoes/editarAluno',['IDEvento'=>$IDEvento,"IDAluno"=>$r->IDUser]).">Abrir</a>";
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

    public function cadastro($id=null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Registro'] = Evento::find($id);
            $view['Contatos'] = json_decode($view['Registro']->Contatos,true);
        }

        return view('Eventos.cadastro',$view);
    }

    public function inscricao($IDEvento){
        return view('Eventos.inscricao',[
            'Evento' => Evento::find($IDEvento)
        ]);
    }

    public function inscrever(Request $request){
        try{
            $data = $request->all();
            $mensagem = 'Inscrição Concluida! O Comprovante será Enviado via Email';
            $data['IDUser'] = Auth::user()->id;
            $aid = $request->IDEvento;
            $rota = 'Eventos/index';
            $status = 'success';
            $Evento = Evento::find($request->IDEvento)->Titulo;
            //dd($data);
            MailController::send(Auth::user()->email,'Confirmação de Inscrição','Mail.confirmacao',array('Evento'=> $Evento));
            Inscricao::create($data);
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDEvento;
            $rota = 'Eventos/Inscricao';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function desinscrever($id){
        Inscricao::find($id)->delete();
    }

    public function delete(Request $request){
        return Evento::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['Contatos'] = json_encode(array_combine($request->Nome,$request->Contato));
            if(!$request->id){
                if($request->file('Capa')){
                    $Foto = $request->file('Capa')->getClientOriginalName();
                    $request->file('Capa')->storeAs('Site',$Foto,'public');
                    $data['Capa'] = $Foto;
                }
                $rota = 'Eventos/Novo';
                $aid = '';
                Evento::create($data);
            }else{
                if($request->file('Capa')){
                    $Foto = $request->file('Capa')->getClientOriginalName();
                    Storage::disk('public')->delete('Site/'.$request->oldCapa);
                    $request->file('Capa')->storeAs('Site',$Foto,'public');
                    $data['Capa'] = $Foto;
                }
                $rota = 'Eventos/Edit';
                $aid = $request->id;
                Evento::find($request->id)->update($data);
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Eventos/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getEventos(){
        $registros = Evento::all();
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Descricao;
                $item[] = Controller::data($r->Inicio,'d/m/Y');
                $item[] = Controller::data($r->Termino,'d/m/Y');
                $item[] = "<a href=".route('Eventos/Edit',$r->id)." class='btn bg-fr text-white btn-xs'>Abrir</a> <a href=".route('Site',['id'=>$r->id,'Nome'=>$r->Titulo])." class='btn bg-fr text-white btn-xs' target='_blank'>Site</a>";
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
