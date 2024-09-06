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
use App\Models\Banca;
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
            $view['Modalidades'] = json_decode($view['Registro']->Modalidades,true);
            $view['Categorias'] = json_decode($view['Registro']->Categorias,true);
            $view['Site'] = json_decode($view['Registro']->Site,true);
        }

        return view('Eventos.cadastro',$view);
    }

    public function inscricao($IDEvento){
        $Evento = Evento::find($IDEvento);
        return view('Eventos.inscricao',[
            'Evento' => Evento::find($IDEvento),
            'Categorias' => json_decode($Evento->Categorias,true)
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

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['Contatos'] = json_encode(array_combine($request->Nome,$request->Contato));
            $data['Categorias'] = json_encode($request->Categoria);
            $data['Modalidades'] = json_encode($request->Modalidade);
            $data['Site'] = json_encode($request->Site);
            if(!$request->id){
                if($request->file('Capa')){
                    $Foto = $request->file('Capa')->getClientOriginalName();
                    $request->file('Capa')->storeAs('Site',$Foto,'public');
                    $data['Capa'] = $Foto;
                }

                if($request->file('ModeloApresentacao')){
                    $Foto = $request->file('ModeloApresentacao')->getClientOriginalName();
                    $request->file('ModeloApresentacao')->storeAs('Site',$Foto,'public');
                    $data['ModeloApresentacao'] = $Foto;
                }
                $rota = 'Eventos/Novo';
                $aid = '';
                $createEvento = Evento::create($data);
                Banca::create([
                    "IDUser"=> Auth::user()->id,
                    "Tipo"=> 1,
                    "IDEvento"=> $createEvento->id
                ]);
                Session::put('IDEvento',$createEvento->id);
            }else{
                if($request->file('Capa')){
                    $Foto = $request->file('Capa')->getClientOriginalName();
                    Storage::disk('public')->delete('Site/'.$request->oldCapa);
                    $request->file('Capa')->storeAs('Site',$Foto,'public');
                    $data['Capa'] = $Foto;
                }

                if($request->file('ModeloApresentacao')){
                    $Foto = $request->file('ModeloApresentacao')->getClientOriginalName();
                    Storage::disk('public')->delete('Site/'.$request->oldModeloApresentacao);
                    $request->file('ModeloApresentacao')->storeAs('Site',$Foto,'public');
                    $data['ModeloApresentacao'] = $Foto;
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
        $currentId = Auth::user()->id;
        $registros = DB::select("SELECT e.Titulo,e.Descricao,e.Inicio,e.Termino,e.id FROM eventos e WHERE e.id IN(SELECT bd.IDEvento FROM bancaevento bd WHERE bd.IDUser = $currentId)");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Descricao;
                $item[] = Controller::data($r->Inicio,'d/m/Y');
                $item[] = Controller::data($r->Termino,'d/m/Y');
                $item[] = "<a href=".route('Eventos/Edit',$r->id)." class='btn bg-fr text-white btn-xs'>Abrir</a> <a href=".route('Site',$r->id)." class='btn bg-fr text-white btn-xs' target='_blank'>Site</a>";
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
