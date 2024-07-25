<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Palestra;
use App\Models\Palestrante;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Telespectador;
use Storage;
class PalestrasController extends Controller
{
    public const submodulos = array([
        "nome" => "Palestras",
        "rota" => "Palestras/index",
        "endereco" => 'index'
    ]);

    public const modulosPalestrantes = array([
        "nome" => "Palestras",
        "rota" => "Palestras/index",
        "endereco" => 'index'
    ]);

    public const submodulosPalestrantes = array([
        "nome" => "Palestrantes",
        "rota" => "Palestras/index",
        "endereco" => 'index'
    ],[
        "nome" => "Telespectadores",
        "rota" => "Palestras/Participantes",
        "endereco" => 'Participantes'
    ]);
    public function index(){
        $view = 'Palestras.index';
        $data = [
            'submodulos' => self::submodulos,
            'id' => ''
        ];
        
        if(Auth::user()->tipo == 3){
            $view = 'Palestras.indexInscrito';
            $AND = '';
            if(Session::has('IDEvento')){
                $AND = ' WHERE pl.IDEvento='.Session::get('IDEvento');
            }
            $data['Palestras'] = DB::select("SELECT pl.*,pa.Nome,e.Titulo as Evento,pa.Foto FROM palestras pl INNER JOIN palestrantes pa ON(pl.IDPalestrante = pa.id) INNER JOIN eventos e ON(e.id = pl.IDEvento) $AND");
        }
        return view($view,$data);
    }

    public function getParticipantesPalestras($IDPalestra){
        $SQL = "SELECT 
                i.id as IDInscrito,
                i.name as Inscrito,
                CASE WHEN tp.IDPalestra IS NOT NULL THEN 'checked' ELSE '' END as Assistiu 
            FROM users i
            LEFT JOIN telespectadores tp ON(i.id = tp.IDInscrito) AND tp.IDPalestra = $IDPalestra
			LEFT JOIN palestras p ON(p.id = tp.IDPalestra)
        ";

        $registros = DB::select($SQL);

        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Inscrito;
                $item[] = "<input type='checkbox' name='IDInscrito[]' $r->Assistiu value='$r->IDInscrito'>";
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

    public function telespectadores($IDPalestra){
        return view("Palestras.telespectadores",[
            "IDPalestra" => $IDPalestra,
            'submodulos' => self::submodulosPalestrantes
        ]);
    }

    public function indexPalestrantes(){
        return view('Palestrantes.index',[
            'submodulos' => self::modulosPalestrantes,
            'id' => ''
        ]);
    }

    public function cadastro($id=null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulosPalestrantes,
            'palestrantes' => Palestrante::all(),
            'eventos' => Evento::all()
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Palestra::find($id);
        }

        return view('Palestras.cadastro', $view);
    }

    public function cadastroPalestrantes($id=null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Palestrante::find($id);
        }

        return view('Palestrantes.cadastro', $view);
    }

    public function delete(Request $request){
        return Palestra::find($request->id)->delete();
    }

    public function deletePalestrantes(Request $request){
        return Palestra::find($request->id)->delete();
    }

    public function save(Request $request){
        try{
            if(!$request->id){
                $rota = 'Palestras/Novo';
                $aid = '';
                Palestra::create($request->all());
            }else{
                $rota = 'Palestras/Edit';
                $aid = $request->id;
                Palestra::find($request->id)->update($request->all());
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Palestras/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function presenca(Request $request){
        try{
            Telespectador::where('IDPalestra',$request->IDPalestra)->delete();
            foreach($request->IDInscrito as $i){
                Telespectador::create([
                    "IDInscrito" => $i,
                    "IDPalestra" => $request->IDPalestra
                ]);
            }
            $rota = 'Palestras/Participantes';
            $aid = $request->IDPalestra;
            $mensagem = "Presença Concluida";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDPalestra;
            $rota = 'Palestras/Participantes';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function savePalestrantes(Request $request){
        try{
            $data = $request->all();
            if(!$request->id){
                $rota = 'Palestrantes/Novo';
                $aid = '';
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    $request->file('Foto')->storeAs('palestrantes',$Foto,'public');
                    $data['Foto'] = $Foto;
                }
                Palestrante::create($data);
            }else{
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    Storage::disk('public')->delete('palestrantes/'.$request->oldFoto);
                    $request->file('Foto')->storeAs('palestrantes',$Foto,'public');
                    $data['Foto'] = $Foto;
                }
                $aid = $request->id;
                Palestrante::find($request->id)->update($data);
            }
            $mensagem = "Salvo";
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Palestrantes/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota, $aid)->with($status, $mensagem);
        }
    }

    public function getPalestras(){
        $registros = DB::select("SELECT pl.*,pa.Nome,e.Titulo as Evento FROM palestras pl INNER JOIN palestrantes pa ON(pl.IDPalestrante = pa.id) INNER JOIN eventos e ON(e.id = pl.IDEvento)");
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Evento;
                $item[] = $r->Titulo;
                $item[] = $r->Nome;
                $item[] = $r->Palestra;
                $item[] = Controller::data($r->Data,'d/m/Y');
                $item[] = $r->Inicio;
                $item[] = $r->Termino;
                $item[] = "<a href=".route('Palestras/Edit',$r->id).">Abrir</a>";
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

    public function getPalestrantes(){
        $registros = Palestrante::all();
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = "<img src='" . url('storage/palestrantes/' . $r->Foto) . "' width='150px' height='100px'>";
                $item[] = $r->Nome;
                $item[] = $r->Curriculo;
                $item[] = "<a href=".route('Palestrantes/Edit',$r->id).">Abrir</a>";
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
