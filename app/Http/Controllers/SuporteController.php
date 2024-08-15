<?php

namespace App\Http\Controllers;
use App\Models\Conversa;
use App\Models\Mensagem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class SuporteController extends Controller
{

    public const submodulos = array([
        'nome' => 'Suporte',
        'rota' => 'Suporte/index',
        'endereco' => 'index'
    ]);

    public function index(){
        return view('Suporte.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public function receber($id){
        $findMensagens = Mensagem::select('Mensagem')->where('IDConversa',$id)->get();
        $mensagem = "";
        foreach($findMensagens as $fm){
            $mensagem .="<div id='chat-box'>".$fm->Mensagem."</div>";
        }

        return $mensagem;
    }

    public function sendMessage(Request $request){
        Mensagem::create($request->all());
    }

    public function cadastro($id=null){
        if($id){
            $view = array(
                "Mensagens" => Mensagem::where('IDConversa',$id)->get(),
                "id" => $id
            );

            $page = "Suporte.chat";
        }else{
            $view = array(
                "submodulos" => self::submodulos
            );

            $page = "Suporte.cadastro";
        }

        return view($page,$view);
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['IDDestinatario'] = 14;
            $data['IDRemetente'] = Auth::user()->id;
            $rota = 'Suporte/index';
            $aid = '';
            Conversa::create($data);
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Suporte/index';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
            //dd($request->IDEvento);
        }
    }

    public function getSuportes(){
        $registros = Conversa::where('IDRemetente',Auth::user()->id)->orWhere('IDDestinatario',Auth::user()->id)->get();
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = date('d/m/Y',strtotime($r->created_at));
                $item[] = "<a href=".route('Suporte/Edit',$r->id)." class='btn bg-fr text-white btn-xs'>Abrir</a>";
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
