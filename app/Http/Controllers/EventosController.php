<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\User;
use App\Mail\Confirmacao;
use App\Models\Inscricao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
            $data['Eventos'] = DB::select("SELECT 
                e.Titulo as Evento,
                e.id as IDEvento,
                e.Descricao as Descricao,
                CASE WHEN e.id = i.IDEvento THEN 1 ELSE 0 END AS Inscrito
                FROM eventos e
                LEFT JOIN inscricoes i ON(i.IDEvento = e.id)
            ");
        }
        return view($view,$data);
    }

    public function inscricoes($IDEvento){
        return view("Eventos.inscritos",[
            "IDEvento" => $IDEvento,
            "submodulos" => self::cadastroSubmodulos
        ]);
    }

    public function inscreverAluno($IDEvento){
        return view('Eventos.inscreverAluno',[
            "IDEvento" => $IDEvento,
            "submodulos" => self::cadastroSubmodulos
        ]);
    }

    public function saveInscricaoAluno(Request $request){
        try{
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
            $resp = Mail::to($request->email)->send(new Confirmacao($request->name, $RandPW));
            dd($resp);
            $mensagem = 'Inscrição Concluida! O Comprovante e os dados de Acesso a Plataforma serão enviados via Email';
            $aid = $request->IDEvento;
            $rota = 'Eventos/Inscricoes/inscreverAluno';
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


    public function getInscricoes($IDEvento){
        $SQL = <<<SQL
            SELECT
                u.name as Nome,
                i.Categoria,
                u.email as Email
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
            $aid = '';
            $rota = 'Eventos/index';
            $status = 'success';
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
            if(!$request->id){
                $rota = 'Eventos/Novo';
                $aid = '';
                Evento::create($request->all());
            }else{
                $rota = 'Eventos/Edit';
                $aid = $request->id;
                Evento::find($request->id)->update($request->all());
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
                $item[] = "<a href=".route('Eventos/Edit',$r->id)." class='btn bg-fr text-white btn-xs'>Abrir</a>";
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
