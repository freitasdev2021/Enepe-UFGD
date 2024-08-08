<?php

namespace App\Http\Controllers;
use App\Models\Palestrante;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Modelo;
use App\Models\User;
use App\Models\Entrega;
use App\Models\Palestra;
use app\Models\Telespectador;
use App\Models\Certificados;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Models\Evento;

class CertificadosController extends Controller
{

    public const submodulos = array([
        "nome" => "Emitir Certificados",
        "rota" => "Certifica/index",
        "endereco" => 'index'
    ],[
        "nome" => "Modelos",
        "rota" => "Certificados/Modelos",
        "endereco" => 'Modelos'
    ]);

    public function index(){
        return view('Certificados.index',[
            'submodulos' => self::submodulos,
            'Modelos' => Modelo::all(),
            "Eventos"=> Evento::all()
        ]);
    }

    public function saveModelo(Request $request){
        try{
            $data = $request->all();
            if($request->file('Arquivo')){
                $Foto = $request->file('Arquivo')->getClientOriginalName();
                $request->file('Arquivo')->storeAs('modelos',$Foto,'public');
                $data['Arquivo'] = $Foto;
            }
            
            Modelo::create($data);
            $aid = '';
            $mensagem = 'Modelo Salvo com Sucesso';
            $status = 'success';
            $rota = 'Certificados/Modelos/Novo';
        }catch(\Throwable $th){
            $aid = '';
            $mensagem = 'Erro: '.$th->getMessage();
            $status = 'error';
            $rota = 'Certificados/Modelos/Novo';
        }finally{
            //dd($data);
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function modelos(){
        return view('Certificados.modelos',[
            'submodulos' => self::submodulos,
            'Modelos' => Modelo::all()
        ]);
    }
    //GERAR CERTIFICADOS
    public function gerarCertificados(Request $request){
        try{
          
            $Modelos = [];
            $Inscrit = [];
            foreach($request->modelo as $m){
                if(!is_null($m)){
                    array_push($Modelos,$m);
                }
            }

            foreach($request->IDInscrito as $i){
                if(!is_null($i)){
                    array_push($Inscrit,$i);
                }
            }
            
            $Evento = Evento::find($request->IDEvento);
            $Certificados = [];
            for($i=0;$i<count($Inscrit);$i++){
                if(!Certificados::where('IDEvento',$request->IDEvento)->where('IDModelo',$Modelos[$i])->where('IDInscrito',$Inscrit[$i])){
                    $Certificados[] = array(
                        "Modelos" => $Modelos[$i],
                        "Inscritos" => $Inscrit[$i]
                    );
                }
            }
            //dd($Certificados);
            foreach ($Certificados as $Certificado) {
                $Modelo = Modelo::find($Certificado['Modelos']);
                //CONTEUDO DO CERTIFICADO
                //dd($Modelo->TPModelo);
                switch($Modelo->TPModelo){
                    case "Organizadores":
                        // if(!str_contains($Modelo->DSModelo,' {organizador} ') || !str_contains($Modelo->DSModelo,' {evento} ') || !User::find($Certificado['Inscritos'])){
                        //     $aid = '';
                        //     $mensagem = "Atenção! Modelo de Organizadores Feito de Maneira Incorreta! ou o Certificado não atende os requisitos de Organizador, favor refaze-lo na aba 'Modelos' ";
                        //     $status = 'error';
                        //     $rota = 'Certifica/index';
                        //     return false;
                        // }
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $STRConteudo = str_replace(['{organizador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                        $Conteudo = explode("|",$STRConteudo);
                        self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->name,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                    break;
                    case "Apresentadores":
                        // if(!str_contains($Modelo->DSModelo,' {apresentador} ') || 
                        // !str_contains($Modelo->DSModelo,' {evento} ') || 
                        // !str_contains($Modelo->DSModelo,' {submissao} ') || 
                        // !str_contains($Modelo->DSModelo,' {palavraschave} ') || 
                        // !str_contains($Modelo->DSModelo,' {autores} ') ||
                        // !User::find($Certificado['Inscritos'])
                        // ){
                        //     $aid = '';
                        //     $mensagem = "Atenção! Modelo de Organizadores Feito de Maneira Incorreta! ou o Certificado não atende os requisitos de Organizador, favor refaze-lo na aba 'Modelos' ";
                        //     $status = 'error';
                        //     $rota = 'Certifica/index';
                        //     return false;
                        // }
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $Trabalho = self::getTrabalho($Certificado['Inscritos'],$request->IDEvento);
                        $STRConteudo = str_replace(['{apresentador}','{evento}','{submissao}','{palavraschave}','{autores}'],[$Inscrito->name,$Evento->Titulo,$Trabalho->Titulo,$Trabalho->palavrasChave,$Trabalho->Autores],$Modelo->DSModelo);
                        $Conteudo = explode("|",$STRConteudo);
                        self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->name,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                    break;
                    case "Telespectadores":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $STRConteudo = str_replace(['{telespectador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                        $Conteudo = explode("|",$STRConteudo);
                        self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->name,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                    break;
                    case "Avaliador de Sessão":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $STRConteudo = str_replace(['{avaliadorsessao}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                        $Conteudo = explode("|",$STRConteudo);
                        self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->name,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                    break;
                    case "Moderador de Sessão":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $STRConteudo = str_replace(['{moderador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                        $Conteudo = explode("|",$STRConteudo);
                        self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->name,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                    break;
                    case "Telespectador de Palestra":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $Assistiu = self::getPalestrasInscrito($Certificado['Inscritos'],$request->IDEvento);
                        //dd($Assistiu);
                        foreach($Assistiu as $as){
                            $STRConteudo = str_replace(['{telespectador}','{palestra}','{evento}'],[$Inscrito->name,$as->Titulo,$Evento->Titulo],$Modelo->DSModelo);
                            $Conteudo = explode("|",$STRConteudo);
                            self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->name,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                        }
                    break;
                    case "Palestrante":
                        $Inscrito = Palestrante::find($Certificado['Inscritos']);
                        $Palestrou = self::getPalestrasPalestrante($Certificado['Inscritos'],$request->IDEvento);
                        //dd($Inscr);
                        foreach($Palestrou as $pa){
                            $STRConteudo = str_replace(['{palestrante}','{palestra}','{evento}'],[$Inscrito->Nome,$pa->Titulo,$Evento->Titulo],$Modelo->DSModelo);
                            $Conteudo = explode("|",$STRConteudo);
                            self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->Nome,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                        }
                    break;
                    case "Avaliadores":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $STRConteudo = str_replace(['{avaliador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                        $Conteudo = explode("|",$STRConteudo);
                        self::setCertificado($Conteudo,$Certificado['Inscritos'],$Modelo->Arquivo,$Inscrito->name,$Evento->Titulo,$request->IDEvento,$Certificado['Modelos']);
                    break;
                }
                //
            }
            $aid = '';
            $mensagem = 'Salvo com Sucesso';
            $status = 'success';
            $rota = 'Certifica/index';
        }catch(\Throwable $th){
            $aid = '';
            $mensagem = 'Erro: '.$th->getMessage();
            $status = 'error';
            $rota = 'Certifica/index';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }
    //PEGAR TRABALHO DO APRESENTADOR
    public function getTrabalho($IDApresentador,$IDEvento){
        return DB::select("SELECT e.Titulo,e.Autores,e.palavrasChave FROM entergas e INNER JOIN submissoes s ON(s.id = e.IDSubmissao) WHERE s.IDEvento = $IDEvento AND e.IDInscrito = $IDApresentador")[0];
    }
    //PEGAR PALESTRAS
    public function getPalestrasInscrito($IDInscrito,$IDEvento){
        return DB::select("SELECT p.Titulo FROM palestras p INNER JOIN telespectadores t ON(t.IDPalestra = p.id) WHERE p.IDEvento = $IDEvento AND t.IDInscrito = $IDInscrito");
    }

    public function getPalestrasPalestrante($IDInscrito,$IDEvento){
        return DB::select("SELECT p.Titulo FROM palestras p INNER JOIN palestrantes pal ON(p.IDPalestrante = pal.id) WHERE p.IDEvento = $IDEvento AND pal.id = $IDInscrito");
    }
    //CRIAR CERTIFICADO
    public function setCertificado($text,$IDInscrito,$Modelo,$Inscrito,$Evento,$IDEvento,$IDModelo){
        //
        $certificatesPath = storage_path('app/public/modelos');
        $publicCertificatesPath = public_path('certificados');
        $certificadoManager = new ImageManager(new Driver());
        $CDCertificado = rand(100000,999999).$IDInscrito;
        array_push($text,"Codigo: ".$CDCertificado);
        // Definir as propriedades da fonte
        $fontSize = 110;
        $lineHeight = $fontSize * 1.2; // Altura da linha
        $x = 1600; // Posição horizontal
        $initialY = 800; // Posição inicial vertical
        $fontPath = public_path('fonts/arial.ttf');
        $certificado = $certificadoManager->read(realpath(storage_path('app/public/modelos/'.$Modelo)));
        // Desenhar cada linha de texto no certificado
        foreach ($text as $index => $line) {
            $y = $initialY + ($lineHeight * $index); // Ajustar a posição vertical para cada linha
            $certificado->text($line, $x, $y, function($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
                $font->color('#000000');
                $font->align('center');
                $font->valign('top');
            });
        }
        // Nome do arquivo do certificado
        $fileName = 'certificado_' . $CDCertificado . '.jpg';
        // Salvar o certificado na pasta de armazenamento
        $certificado->save($certificatesPath . '/' . $fileName);
        // Copiar o certificado para a pasta pública
        copy($certificatesPath . '/' . $fileName, $publicCertificatesPath . '/' . $fileName);
        //salvar no banco
        Certificados::create([
            "Certificado" => $fileName,
            "IDInscrito" => $IDInscrito,
            "IDEvento" => $IDEvento,
            "Codigo" => $CDCertificado,
            "IDModelo" => $IDModelo
        ]);
    }

    public function validarCertificados(Request $request){
        try{
            if(!Certificados::where('Codigo',$request->Codigo)){
                $status = 'error';
                $mensagem = 'Certificado Inexistente';
                return false;
            }
            $status ='success';
            $mensagem = 'Certificado Valido!';
        }catch(\Throwable $th){
            $aid = '';
            $mensagem = 'Erro: '.$th->getMessage();
            $status = 'error';
            $rota = 'dashboard';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }

    }

    public function getSelectModelos($IDInscrito,$modelo){
        ob_start();
        ?>
        <select name="modelo[]" data-inscrito="<?=$IDInscrito?>" class="form-control col-auto selectModelo" onchange="setInscrito(this.getAttribute('data-inscrito'))">
            <option value="">Selecione um Modelo</option>
            <?php foreach(Modelo::all() as $m){?>
            <option value="<?=$m->id?>" <?=($modelo == $m->id) ? 'selected' : ''?>><?=$m->Nome?></option>
           <?php } ?>
        </select>
        <?php
        return ob_get_clean();
    }

    public function getCertificados(){

        if(isset($_GET['evento'])){
            $evento = $_GET['evento'];
        }else{
            $evento = 0;
        }

        if(isset($_GET['Tipo']) && $_GET['Tipo'] == 'Palestrantes'){
            $SQL = <<<SQL
                SELECT 
                    p.Nome,
                    p.Email,
                    p.id as IDInscrito,
                    c.IDModelo
                FROM palestrantes p
                INNER JOIN palestras pal ON(p.id = pal.IDPalestrante)
                LEFT JOIN certificados c ON(p.id = c.IDInscrito)
                LEFT JOIN modelos m ON(m.id = c.IDModelo)
                WHERE pal.IDEvento = $evento
            SQL;
        }elseif(isset($_GET['Tipo']) && $_GET['Tipo'] == 'Inscritos'){
            $SQL = <<<SQL
                SELECT 
                    u.name as Nome,
                    u.Email as Email,
                    u.id as IDInscrito,
                    MAX(c.IDModelo) as IDModelo
                FROM 
                    users u
                INNER JOIN 
                    inscricoes i ON i.IDUser = u.id
                LEFT JOIN 
                    certificados c ON u.id = c.IDInscrito
                LEFT JOIN 
                    modelos m ON m.id = c.IDModelo
                WHERE 
                    i.IDEvento = $evento
                GROUP BY 
                    u.name, u.Email, u.id;
            SQL;
        }elseif(isset($_GET['Tipo']) && $_GET['Tipo'] == 'Organizadores e Avaliadores'){
            $SQL = <<<SQL
                SELECT 
                    u.name as Nome,
                    u.Email as Email,
                    u.id as IDInscrito,
                    c.IDModelo
                FROM users u
                LEFT JOIN certificados c ON(u.id = c.IDInscrito)
                LEFT JOIN modelos m ON(m.id = c.IDModelo)
                WHERE u.tipo IN(1,2)
            SQL;
        }else{
            $SQL = <<<SQL
                SELECT 
                    u.name as Nome,
                    u.Email as Email,
                    u.id as IDInscrito,
                    c.IDModelo
                FROM users u
                INNER JOIN inscricoes i ON(i.IDUser = u.id)
                LEFT JOIN certificados c ON(u.id = c.IDInscrito)
                LEFT JOIN modelos m ON(m.id = c.IDModelo)
                WHERE i.IDEvento = $evento
            SQL;
        }

        $registros = DB::select($SQL);

        $itensJSON = [];
        if (count($registros) > 0) {
            foreach ($registros as $r) {
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Email;
                $item[] = self::getSelectModelos($r->IDInscrito,$r->IDModelo)."<input type='hidden' id='inscrito_$r->IDInscrito' name='IDInscrito[]'>";
                $itensJSON[] = $item;
            }
        }

        $resultados = [
            "recordsTotal" => intval(count($registros)),
            "recordsFiltered" => intval(count($registros)),
            "data" => $itensJSON 
        ];

        echo json_encode($resultados);

    }

    public function cadastroModelos(){
        return view('Certificados.cadastro');
    }
}
