<?php

namespace App\Http\Controllers;
use App\Models\Palestrante;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Modelo;
use App\Models\User;
use FPDF;
use App\Models\Certificados;
use Intervention\Image\ImageManager;
use Storage;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Drivers\Gd\Driver;
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
            'Modelos' => Modelo::all()->where("IDEvento",Session::get("IDEvento")),
            "Eventos"=> Evento::all()
        ]);
    }

    public function convertJpgToPdf($certificado)
    {
        // Caminho da imagem JPG
        $imagePath = storage_path('app/public/modelos/'.$certificado);

        // Verifica se o arquivo existe
        if (!file_exists($imagePath)) {
            return response()->json(['error' => 'Arquivo não encontrado.'], 404);
        }

        // Instancia o mPDF
        // Cria o PDF com FPDF
        $pdf = new FPDF('L', 'mm', array(508, 286)); // Configura o tamanho da página para 1920x1080 pixels em mm
        $pdf->AddPage();
        $pdf->Image($imagePath, 10, 10, 508 - 20);

        // Define o nome do arquivo PDF
        $fileName = 'certificado.pdf';

        // Gera o PDF e retorna como download
        return response()->stream(
            function () use ($pdf) {
                echo $pdf->Output('', 'S');
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
            ]
        );
        
    }

    public function enviarCertificadoEmail($email,$certificado)
    {
        // Caminho da imagem JPG
        $imagePath = storage_path('app/public/modelos/'.$certificado);

        // Verifica se o arquivo existe
        if (!file_exists($imagePath)) {
            return response()->json(['error' => 'Arquivo não encontrado.'], 404);
        }


        // Instancia o mPDF
        // Cria o PDF com FPDF
        $pdf = new FPDF('L', 'mm', array(508, 286)); // Configura o tamanho da página para 1920x1080 pixels em mm
        $pdf->AddPage();
        $pdf->Image($imagePath, 10, 10, 508 - 20);

        // Define o nome do arquivo PDF
        $fileName = 'certificado.pdf';

        // Gera o PDF e envia via email
        $anexoAqui = $pdf->Output('','S');
        MailController::sendAnexo($anexoAqui,"Certificado Enviado",$email);
        return redirect()->back();
    }

    public function saveModelo(Request $request){
        try{
            $data = $request->all();
            $data['IDEvento'] = Session::get('IDEvento');
            //CONFERÊNCIA DE ERROS
            if($request->id){
                if($request->file('Arquivo')){
                    $Foto = $request->file('Arquivo')->getClientOriginalName();
                    Storage::disk('public')->delete('modelos/'.$request->oldModelo);
                    $request->file('Arquivo')->storeAs('modelos',$Foto,'public');
                    $data['Arquivo'] = $Foto;
                }
                Modelo::find($data['id'])->update($data);
            }else{
                if($request->file('Arquivo')){
                    $Foto = $request->file('Arquivo')->getClientOriginalName();
                    $request->file('Arquivo')->storeAs('modelos',$Foto,'public');
                    $data['Arquivo'] = $Foto;
                }
                Modelo::create($data);
            }
            $status = 'success';
            $mensagem = 'Modelo Salvo com Sucesso';
            $aid = '';
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
            'Modelos' => Modelo::all()->where("IDEvento",Session::get("IDEvento"))
        ]);
    }
    //GERAR CERTIFICADOS
    public function gerarCertificados(Request $request){
        try{
          
            $Modelos = [];
            $Inscrit = [];
            foreach($request->modelo as $m){
                //if(!is_null($m)){
                    array_push($Modelos,$m);
                //}
            }

            foreach($request->IDInscrito as $i){
                //if(!is_null($i)){
                    array_push($Inscrit,$i);
                //}
            }
            
            $Evento = Evento::find(Session::get('IDEvento'));
            $Certificados = [];
            for($i=0;$i<count($Inscrit);$i++){
                $existeCertificado = Certificados::where('IDEvento', Session::get('IDEvento'))
                        ->where('IDModelo', $Modelos[$i])
                        ->where('IDInscrito', $Inscrit[$i])
                        ->exists();
                if(!$existeCertificado && !is_null($Modelos[$i]) && !is_null($Inscrit[$i])){
                    $Certificados[] = array(
                        "Modelos" => $Modelos[$i],
                        "Inscritos" => $Inscrit[$i]
                    );
                }
            }
           
            $emissao = [];
            $erros = [];
            //VALIDAÇÃO DO PREENCHIMENTO
            foreach ($Certificados as $Certificado){
                $Modelo = Modelo::find($Certificado['Modelos']);
                //CONTEUDO DO CERTIFICADO
                //dd($Modelo->TPModelo);
                switch($Modelo->TPModelo){
                    case "Organizadores":
                        //dd("teste");
                        $Inscrito = User::find($Certificado['Inscritos']);
                        if(!str_contains($Modelo->DSModelo,'{organizador}') || !str_contains($Modelo->DSModelo,'{evento}') || !$Inscrito){
                            $STRConteudo = "";
                            if(!str_contains($Modelo->DSModelo,'{organizador}')){
                                $STRConteudo .= str_replace(['{evento}'],[$Evento->Titulo],$Modelo->DSModelo);
                            }

                            if(!str_contains($Modelo->DSModelo,'{evento}')){
                                $STRConteudo .= str_replace(['{organizador}'],[$Inscrito->name],$Modelo->DSModelo);
                            }

                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }else{
                            $STRConteudo = str_replace(['{organizador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }
                    break;
                    case "Apresentadores":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $Trabalho = self::getTrabalho($Certificado['Inscritos'],Session::get('IDEvento'));
                        //
                        if(!str_contains($Modelo->DSModelo,'{apresentador}') || 
                        !str_contains($Modelo->DSModelo,'{evento}') || 
                        !str_contains($Modelo->DSModelo,'{submissao}') || 
                        !str_contains($Modelo->DSModelo,'{palavraschave}') || 
                        !str_contains($Modelo->DSModelo,'{autores}') ||
                        !$Inscrito ||
                        !$Trabalho
                        ){

                            $STRConteudo = $Modelo->DSModelo;

                            // Substituir tags uma única vez
                            $substituicoes = [
                                '{evento}' => $Evento->Titulo,
                                '{submissao}' => $Trabalho->Titulo,
                                '{palavraschave}' => $Trabalho->palavrasChave,
                                '{autores}' => $Trabalho->Autores,
                                '{apresentador}' => $Inscrito->name
                            ];

                            foreach ($substituicoes as $tag => $valor) {
                                if (str_contains($STRConteudo, $tag)) {
                                    $STRConteudo = str_replace($tag, $valor, $STRConteudo);
                                }
                            }

                            // Remove as tags não substituídas, se necessário
                            $tagsNaoSubstituidas = array_keys($substituicoes);
                            $STRConteudo = str_replace($tagsNaoSubstituidas, '', $STRConteudo);

                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }else{
                            $STRConteudo = str_replace(['{apresentador}','{evento}','{submissao}','{palavraschave}','{autores}'],[$Inscrito->name,$Evento->Titulo,$Trabalho->Titulo,$Trabalho->palavrasChave,$Trabalho->Autores],$Modelo->DSModelo);
                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }
                        //
                    break;
                    case "Telespectadores":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        //
                        if(!str_contains($Modelo->DSModelo,'{telespectador}') || !str_contains($Modelo->DSModelo,'{evento}') || !$Inscrito){
                            
                            $STRConteudo = "";

                            if(!str_contains($Modelo->DSModelo,'{telespectador}')){
                                $STRConteudo .= str_replace(['{evento}'],[$Evento->Titulo],$Modelo->DSModelo);
                            }
                           
                            if(!str_contains($Modelo->DSModelo,'{evento}')){
                                $STRConteudo .= str_replace(['{telespectador}'],[$Inscrito->name],$Modelo->DSModelo);
                            }

                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }else{
                            $STRConteudo = str_replace(['{telespectador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }
                    break;
                    case "Avaliador de Sessão":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        //
                        if(!str_contains($Modelo->DSModelo,'{avaliadorsessao}') || !str_contains($Modelo->DSModelo,'{evento}') || !$Inscrito){

                            $STRConteudo = "";

                            if(!str_contains($Modelo->DSModelo,'{avaliadorsessao}')){
                                $STRConteudo .= str_replace(['{evento}'],[$Evento->Titulo],$Modelo->DSModelo);
                            }

                            if(!str_contains($Modelo->DSModelo,'{evento}')){
                                $STRConteudo .= str_replace(['{avaliadorsessao}'],[$Inscrito->name],$Modelo->DSModelo);
                            }

                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }else{
                            $STRConteudo = str_replace(['{avaliadorsessao}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }
                        //
                    break;
                    case "Moderador de Sessão":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        //
                        if(!str_contains($Modelo->DSModelo,'{moderador}') || !str_contains($Modelo->DSModelo,'{evento}') || !$Inscrito){
                            $STRConteudo = "";

                            if(!str_contains($Modelo->DSModelo,'{moderador}')){
                                $STRConteudo .= str_replace(['{evento}'],[$Evento->Titulo],$Modelo->DSModelo);
                            }

                            if(!str_contains($Modelo->DSModelo,'{evento}')){
                                $STRConteudo .= str_replace(['{moderador}'],[$Inscrito->name],$Modelo->DSModelo);
                            }

                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                            
                        }else{
                            $STRConteudo = str_replace(['{moderador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }
                        //
                    break;
                    case "Telespectador de Palestra":
                        $Inscrito = User::find($Certificado['Inscritos']);
                        $Assistiu = self::getPalestrasInscrito($Certificado['Inscritos'],Session::get('IDEvento'));
                        //
                        if(!str_contains($Modelo->DSModelo,'{telespectadorpalestra}') || !str_contains($Modelo->DSModelo,'{evento}') || !$Assistiu || !str_contains($Modelo->DSModelo,'{palestra}') || !$Inscrito){
                            // dd($Assistiu);
                            foreach($Assistiu as $as){
                                $STRConteudo = $Modelo->DSModelo;

                                // Substituir tags uma única vez
                                $substituicoes = [
                                    '{evento}' => $Evento->Titulo,
                                    '{palestra}' => $as->Titulo,
                                    '{telespectadorpalestra}' => $Inscrito->name
                                ];

                                foreach ($substituicoes as $tag => $valor) {
                                    if (str_contains($STRConteudo, $tag)) {
                                        $STRConteudo = str_replace($tag, $valor, $STRConteudo);
                                    }
                                }

                                // Remove as tags não substituídas, se necessário
                                $tagsNaoSubstituidas = array_keys($substituicoes);
                                $STRConteudo = str_replace($tagsNaoSubstituidas, '', $STRConteudo);

                                $Conteudo = wordwrap($STRConteudo,50,"|");
                                $emissao[] = array(
                                    "Conteudo" => $Conteudo,
                                    "IDInscrito" => $Certificado['Inscritos'],
                                    "Arquivo" => $Modelo->Arquivo,
                                    "Inscrito" => $Inscrito->name,
                                    "Evento" => $Evento->Titulo,
                                    "IDEvento"=> Session::get('IDEvento'),
                                    "Modelo" => $Certificado['Modelos'] 
                                );
                            }
                        }else{
                            foreach($Assistiu as $as){
                                $STRConteudo = str_replace(['{telespectadorpalestra}','{evento}','{palestra}'],[$Inscrito->name,$Evento->Titulo,$as->Titulo],$Modelo->DSModelo);
                                $Conteudo = wordwrap($STRConteudo,50,"|");
                                $emissao[] = array(
                                    "Conteudo" => $Conteudo,
                                    "IDInscrito" => $Certificado['Inscritos'],
                                    "Arquivo" => $Modelo->Arquivo,
                                    "Inscrito" => $Inscrito->name,
                                    "Evento" => $Evento->Titulo,
                                    "IDEvento"=> Session::get('IDEvento'),
                                    "Modelo" => $Certificado['Modelos'] 
                                );
                            }
                        }
                        //
                    break;
                    case "Palestrante":
                        $Inscrito = Palestrante::find($Certificado['Inscritos']);
                        $Palestrou = self::getPalestrasPalestrante($Certificado['Inscritos'],Session::get('IDEvento'));
                        //
                        if(!str_contains($Modelo->DSModelo,'{palestrante}') || !str_contains($Modelo->DSModelo,'{evento}') || !$Palestrou || !str_contains($Modelo->DSModelo,'{palestra}') || !$Inscrito){
                            foreach($Palestrou as $pa){

                                $STRConteudo = $Modelo->DSModelo;

                                // Substituir tags uma única vez
                                $substituicoes = [
                                    '{evento}' => $Evento->Titulo,
                                    '{palestrante}' => $Inscrito->Nome,
                                    '{palestra}' => $pa->Titulo
                                ];

                                foreach ($substituicoes as $tag => $valor) {
                                    if (str_contains($STRConteudo, $tag)) {
                                        $STRConteudo = str_replace($tag, $valor, $STRConteudo);
                                    }
                                }

                                // Remove as tags não substituídas, se necessário
                                $tagsNaoSubstituidas = array_keys($substituicoes);
                                $STRConteudo = str_replace($tagsNaoSubstituidas, '', $STRConteudo);

                                $Conteudo = wordwrap($STRConteudo,50,"|");
                                $emissao[] = array(
                                    "Conteudo" => $Conteudo,
                                    "IDInscrito" => $Certificado['Inscritos'],
                                    "Arquivo" => $Modelo->Arquivo,
                                    "Inscrito" => $Inscrito->name,
                                    "Evento" => $Evento->Titulo,
                                    "IDEvento"=> Session::get('IDEvento'),
                                    "Modelo" => $Certificado['Modelos'] 
                                );
                            }
                        }else{
                            foreach($Palestrou as $pa){
                                $STRConteudo = str_replace(['{palestrante}','{palestra}','{evento}'],[$Inscrito->Nome,$pa->Titulo,$Evento->Titulo],$Modelo->DSModelo);
                                $Conteudo = wordwrap($STRConteudo,50,"|");
                                $emissao[] = array(
                                    "Conteudo" => $Conteudo,
                                    "IDInscrito" => $Certificado['Inscritos'],
                                    "Arquivo" => $Modelo->Arquivo,
                                    "Inscrito" => $Inscrito->name,
                                    "Evento" => $Evento->Titulo,
                                    "IDEvento"=> Session::get('IDEvento'),
                                    "Modelo" => $Certificado['Modelos'] 
                                );
                            }
                        }
                        //
                    break;
                    case "Avaliadores":
                        $Inscrito = User::find($Certificado['Inscritos']);

                        if(!str_contains($Modelo->DSModelo,'{avaliador}') || !str_contains($Modelo->DSModelo,'{evento}') || !$Inscrito){
                            $STRConteudo = "";
                            if(!str_contains($Modelo->DSModelo,'{avaliador}')){
                                $STRConteudo .= str_replace(['{evento}'],[$Evento->Titulo],$Modelo->DSModelo);
                            }

                            if(!str_contains($Modelo->DSModelo,'{evento}')){
                                $STRConteudo .= str_replace(['{avaliador}'],[$Inscrito->name],$Modelo->DSModelo);
                            }

                            $Conteudo = wordwrap($STRConteudo,50,"|");
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }else{
                            $STRConteudo = str_replace(['{avaliador}','{evento}'],[$Inscrito->name,$Evento->Titulo],$Modelo->DSModelo);
                            $Conteudo = wordwrap($STRConteudo,50,"|");
    
                            $emissao[] = array(
                                "Conteudo" => $Conteudo,
                                "IDInscrito" => $Certificado['Inscritos'],
                                "Arquivo" => $Modelo->Arquivo,
                                "Inscrito" => $Inscrito->name,
                                "Evento" => $Evento->Titulo,
                                "IDEvento"=> Session::get('IDEvento'),
                                "Modelo" => $Certificado['Modelos'] 
                            );
                        }
                    
                    break;
                }
                //
            }
            //
            if(count($erros) == 0){
                foreach($emissao as $e){
                    self::setCertificado(explode("|",$e['Conteudo']),$e['IDInscrito'],$e['Arquivo'],$e['Inscrito'],$e['Evento'],$e['IDEvento'],$e['Modelo']);
                }
                $mensagem = 'Salvo com Sucesso';
                $status = 'success';
            }else{
                $mensagem = 'Houveram Erros de Preenchimento de Certificado';
                
                foreach($erros as $e){
                    $mensagem .= $e;
                }
                
                $status = 'error';
            }
            //
            $aid = '';
            
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

    public function delCertificado($numero){
        Certificados::where('Codigo',$numero)->delete();
        Storage::disk('public')->delete('modelos/'.'certificado_'.$numero);
    }

    //DELETE CERTIFICADO
    public function delete($id){
        Modelo::find($id)->delete();
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
        //dd(explode('|',$text));
        array_push($text,"Codigo: ".$CDCertificado);
        // Definir as propriedades da fonte
        $fontSize = 50;
        $lineHeight = $fontSize * 1.2; // Altura da linha
        $x = 1000; // Posição horizontal
        $initialY = 450; // Posição inicial vertical
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
        //copy($certificatesPath . '/' . $fileName, $publicCertificatesPath . '/' . $fileName);
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
        $evento = Session::get("IDEvento");
        $Emitiu = Certificados::where('IDEvento',$evento)->where('IDInscrito',$IDInscrito)->exists();
        ?>
        <select name="modelo[]" data-inscrito="<?=$IDInscrito?>" class="form-control col-auto selectModelo">
            <option value="">Selecione um Modelo</option>
            <?php foreach(Modelo::all()->where("IDEvento",Session::get("IDEvento")) as $m){?>
            <option value="<?=$m->id?>" <?=($modelo == $m->id && $Emitiu) ? 'selected' : ''?>><?=$m->Nome?></option>
           <?php } ?>
        </select>
        <?php
        return ob_get_clean();
    }

    public function getCertificados(){

        $evento = Session::get('IDEvento');

        if(isset($_GET['Tipo']) && $_GET['Tipo'] == 'Palestrantes'){
            $SQL = <<<SQL
                SELECT 
                p.Nome,
                p.Email,
                p.id as IDInscrito,
                MAX(c.Codigo) as Codigo,
                MAX(c.IDModelo) as IDModelo,
                MAX(c.Certificado) as Certificado,
                MAX(c.Disponibilidade) as Disponibilidade,
                (SELECT COUNT(c2.id) FROM certificados c2 WHERE c2.IDEvento = $evento AND c2.IDInscrito = u.id) as Certificou
                FROM 
                    palestrantes p
                INNER JOIN 
                    palestras pal ON p.id = pal.IDPalestrante
                LEFT JOIN 
                    certificados c ON p.id = c.IDInscrito
                LEFT JOIN 
                    modelos m ON m.id = c.IDModelo
                WHERE 
                    pal.IDEvento = $evento
                GROUP BY 
                    c.IDModelo, -- Agrupando pelo modelo de certificado
                    u.name, 
                    u.Email,
                    u.id,
                    c.Certificado,
                    c.Disponibilidade,
                    c.Codigo
                SQL;
                $registros = DB::select($SQL);
        }elseif(isset($_GET['Tipo']) && $_GET['Tipo'] == 'Inscritos'){
            $SQL = <<<SQL
                SELECT 
                    u.name as Nome,
                    u.Email as Email,
                    u.id as IDInscrito,
                    c.Certificado as Certificado,
                    c.Disponibilidade,
                    (SELECT COUNT(c2.id) 
                        FROM certificados c2 
                        WHERE c2.IDEvento = $evento 
                        AND c2.IDInscrito = u.id) as Certificou,
                    c.IDModelo as IDModelo,
                    c.Codigo
                FROM 
                    users u
                INNER JOIN 
                    inscricoes i ON i.IDUser = u.id
                LEFT JOIN 
                    certificados c ON u.id = c.IDInscrito AND c.IDEvento = $evento
                LEFT JOIN 
                    modelos m ON m.id = c.IDModelo
                WHERE 
                    i.IDEvento = $evento
                GROUP BY 
                    c.IDModelo, -- Agrupando pelo modelo de certificado
                    u.name, 
                    u.Email,
                    u.id,
                    c.Certificado,
                    c.Disponibilidade,
                    c.Codigo
            SQL;
            $registros = DB::select($SQL);
        }elseif(isset($_GET['Tipo']) && $_GET['Tipo'] == 'Organizadores e Avaliadores'){
            $SQL = <<<SQL
                SELECT 
                    u.name as Nome,
                    u.Email as Email,
                    u.id as IDInscrito,
                    c.Disponibilidade,
                    (SELECT COUNT(c2.id) FROM certificados c2 WHERE c2.IDEvento = $evento AND c2.IDInscrito = u.id) as Certificou,
                    c.IDModelo,
                    c.Certificado,
                    c.Codigo
                FROM users u
                LEFT JOIN certificados c ON(u.id = c.IDInscrito)
                LEFT JOIN modelos m ON(m.id = c.IDModelo)
                WHERE u.tipo IN(1,2) AND u.id IN(SELECT IDUser FROM bancaevento be WHERE be.IDEvento = $evento)
            SQL;
            $registros = DB::select($SQL);
        }elseif(isset($_GET['Tipo']) && $_GET['Tipo'] == 'Fizeram a Avaliação'){
            $SQL = <<<SQL
                SELECT 
                    u.name as Nome,
                    u.Email as Email,
                    u.id as IDInscrito,
                    c.IDModelo,
                    (SELECT COUNT(c2.id) FROM certificados c2 WHERE c2.IDEvento = $evento AND c2.IDInscrito = u.id) as Certificou,
                    c.Disponibilidade,
                    c.Certificado,
                    c.Codigo
                FROM users u
                LEFT JOIN certificados c ON(u.id = c.IDInscrito)
                LEFT JOIN modelos m ON(m.id = c.IDModelo)
                WHERE u.id IN(SELECT IDUser FROM formularios f INNER JOIN respostas r ON(f.id = r.IDForm) WHERE f.IDEvento = $evento)
            SQL;
            $registros = DB::select($SQL);
        }else{
            $registros = [];
        }

        $itensJSON = [];
        if (count($registros) > 0) {
            foreach ($registros as $r) {
                $RemoveCRT = !empty($r->Certificado) ? '"'. strval(route('Certificados/Excluir',$r->Codigo)). '"' : 0;
                $urlPef = '"'.url('storage/modelos/'.$r->Certificado).'"';
                $item = [];
                $item[] = ($r->Disponibilidade == 0) ? "Indisponivel para o Aluno" : 'Disponivel para o Aluno';
                $item[] = $r->Nome;
                $item[] = $r->Email;
                $item[] = self::getSelectModelos($r->IDInscrito,$r->IDModelo)."<input type='hidden' value='$r->IDInscrito' id='inscrito_$r->IDInscrito' name='IDInscrito[]'>";
                $item[] = !empty($r->Certificado) && $r->Certificou > 0 ? "<a href=".url('storage/modelos/'.$r->Certificado)." class='btn btn-fr btn-xs text-white' download>Baixar</a> 
                <button type='button' onclick='convertJpgToPdf($urlPef)' class='btn btn-xs bg-fr text-white'>Baixar PDF</button>
                <a class='btn btn-fr btn-xs text white' href=".route('Certificados/Email',['email'=>$r->Email,'certificado'=>$r->Certificado]).">Enviar por Email</a> 
                <button type='button' class='btn btn-fr btn-xs text-white' onclick='delCertificado($RemoveCRT)'>Excluir</button>
                <a href=".url('storage/modelos/'.$r->Certificado)." class='btn btn-fr btn-xs text-white' target='_blank'>Abrir</a>" : 'Ainda Não Emitido';
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

    public function disponibilizar(){
        Certificados::where('Disponibilidade',0)->where('IDEvento',Session::get('IDEvento'))->update(['Disponibilidade'=>1]);
        return redirect()->route('Certifica/index');
    }

    public function cadastroModelos($id=null){
        $view = array();
        if($id){
            $view = array(
                "Registro"=> Modelo::find($id)
            );
        }
        return view('Certificados.cadastro',$view);
    }
}
