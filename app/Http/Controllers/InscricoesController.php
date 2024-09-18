<?php

namespace App\Http\Controllers;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Http\Request;

class InscricoesController extends Controller
{

    public const submodulos = array([
        "rota" => "Inscricoes/index",
        "endereco" => "index",
        "nome" => "Inscricoes"
    ]);

    public function index(){
        $Evento = Evento::find(Session::get('IDEvento'));
        return view("Inscricoes.index",[
            "submodulos" => self::submodulos,
            'Categorias' => json_decode($Evento->Categorias,true)
        ]);
    }


    public function cadastro($IDAluno=null){
        $Evento = Evento::find(Session::get('IDEvento'));
        $view = [
            "IDEvento" => $Evento->id,
            "submodulos" => self::submodulos,
            'Categorias' => json_decode($Evento->Categorias,true)
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
                WHERE i.IDEvento = $Evento->id AND i.IDUser = $IDAluno
            SQL;
            $view['Registro'] = DB::select($SQL)[0];
        }
        return view('Inscricoes.cadastro',$view);
    }

    public function save(Request $request){
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
                $rota = 'Inscricoes/Novo';
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
                $rota = 'Inscricoes/Edit';
                $aid = $request->id;
            }
            
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = '';
            $rota = 'Inscricoes/Novo';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getInscricoes(){
        $IDEvento = Session::get("IDEvento");
        $SQL = <<<SQL
            SELECT
                u.name as Nome,
                i.Categoria,
                i.id as IDInscricao,
                u.email as Email,
                u.id as IDUser,
                MAX(CASE WHEN c.id IS NULL THEN 0 ELSE 1 END) as Certificou,
                MAX(CASE WHEN e.id IS NULL THEN 0 ELSE 1 END) as Entregou
            FROM inscricoes i
            INNER JOIN users u ON u.id = i.IDUser
            LEFT JOIN certificados c ON u.id = c.IDInscrito
            LEFT JOIN entergas e ON e.IDInscrito = u.id
            WHERE i.IDEvento = $IDEvento
            GROUP BY u.id, u.name, i.Categoria, i.id, u.email;
        SQL;
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $ApagarInscrito = '"'.route('Inscricoes/Excluir',$r->IDInscricao).'"';
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Categoria;
                $item[] = $r->Email;
                $item[] = "<a href=".route('Inscricoes/Edit',$r->IDUser).">Abrir</a> <button class='btn btn-danger text-white btn-xs' onclick='apagarInscrito($ApagarInscrito,$r->Certificou,$r->Entregou)'>Apagar</button>";
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

    public function apagaInscrito($IDInscrito){
        $IDUser = Inscricao::find($IDInscrito)->IDUser;
        Inscricao::find($IDInscrito)->delete();
        User::find($IDUser)->delete();
     }

}
