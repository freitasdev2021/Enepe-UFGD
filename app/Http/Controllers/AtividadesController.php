<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ZoomController;
use App\Models\Atividade;
use App\Models\Sala;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
class AtividadesController extends Controller
{
    public const submodulos = array([
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

    public function index($IDEvento){
        return view('Atividades.index', [
            'submodulos' => self::submodulos,
            'IDEvento' => $IDEvento
        ]);
    }

    public function indexInscrito(){
        $AND = '';
        if(Session::has('IDEvento')){
            $AND = ' WHERE s.IDEvento='.Session::get('IDEvento');
        }
        return view('Atividades.indexInscrito',[
            'Atividades' => DB::select("SELECT a.id,a.Titulo,s.Sala,a.Descricao,a.Inicio FROM atividades a INNER JOIN salas s ON(a.IDSala = s.id) $AND")
        ]);
    }

    public function cadastro($IDEvento,$id = null){
        $view = array(
            'id' => '',
            'IDEvento' => $IDEvento,
            'submodulos' => self::submodulos,
            'Salas' => Sala::all()->where('IDEvento',$IDEvento)
        );

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::submodulos;
            $view['Registro'] = Atividade::find($id);
        }

        return view('Atividades.cadastro', $view);
    }

    public function generateZoomSignature(Request $request)
    {
        $zoomService = new ZoomController();
        $meetingNumber = $request->input('meetingNumber');
        $role = $request->input('role'); // 0 para participantes, 1 para anfitriões

        // Obtenha um token de acesso válido usando OAuth Server-to-Server
        $accessToken = $zoomService->getStoredOrRenewZoomAccessToken();

        // Gere a assinatura para ingresso na reunião
        $signature = $this->generateZoomMeetingSignature($meetingNumber, $role, $accessToken);

        return response()->json([
            'signature' => $signature,
        ]);
    }

    public function generateZoomMeetingSignature($meetingNumber, $role, $accessToken)
    {
        $apiKey = env('ZOOM_CLIENT_ID');
        $apiSecret = env('ZOOM_CLIENT_SECRET');

        $payload = array(
            'iss' => $apiKey,
            'exp' => strtotime('+1 hour'),
        );

        $token = JWT::encode($payload, $apiSecret,'HS256');

        return $token;
    }

    public function atividade($IDAtividade){
        return view('Salas.sala',[
            'Sala' => Atividade::find($IDAtividade),
            'Nome' => Auth::user()->name,
            'Email' => Auth::user()->email
        ]);
    }

    public function delete(Request $request){
        return Atividade::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            if(!$request->id){

                //dd($accessToken);
                ////
                $rota = 'Eventos/Atividades/Novo';
                $aid = $request->IDEvento;
                $zoomService = new ZoomController();
                $accessToken = $zoomService->getAccessToken();
                $meetingData = [
                    'topic' => $request->Titulo,
                    'type' => 2,
                    'start_time' => $request->Inicio,
                    // 'duration' => 30,
                    'timezone'=> "America/Campo_Grande",
                    'password' => '123',
                    'settings' => [
                        'join_before_host' => true, // Permitir ingresso antes do host
                        'host_video' => true,
                        'participant_video' => true,
                        'mute_upon_entry' => true,
                        'waiting_room' => false,
                    ]
                ];
                $meeting = $zoomService->createMeeting($accessToken, $meetingData);
                $data['PWMeeting'] = 123;
                $data['IDMeeting'] = $meeting['id'];
                $data['URLMeeting'] = $meeting['join_url'];
                
                Atividade::create($data);
            }else{
                $rota = 'Eventos/Atividades/Edit';
                $aid = array("IDEvento" => $request->IDEvento,"id"=>$request->id);
                Atividade::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDEvento;
            $rota = 'Eventos/Atividades/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
            //dd($request->IDEvento);
        }
    }

    public function getAtividades($IDEvento){
        $registros = DB::select("SELECT s.IDEvento,a.id,a.Titulo,s.Sala,a.Descricao,a.Inicio FROM atividades a INNER JOIN salas s ON(a.IDSala = s.id)");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Sala;
                $item[] = $r->Descricao;
                $item[] = $r->Inicio;
                $item[] = "<a href=".route('Eventos/Atividades/Edit',['id'=>$r->id,'IDEvento'=>$r->IDEvento])." class='btn bg-fr text-white btn-xs'>Abrir</a>";
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
