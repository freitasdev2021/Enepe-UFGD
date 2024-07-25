<?php

namespace App\Http\Controllers;
use App\Models\Palestrante;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Modelo;
use App\Models\User;
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

    public function gerarCertificados(Request $request){
        try{
            $studentIds = $request->IDParticipante;
            $students = ($request->Tipo == 'Palestrantes') ? Palestrante::whereIn('id',$studentIds)->get() : User::whereIn('id', $studentIds)->get();
            $certificatesPath = storage_path('app/public/modelos');
            $publicCertificatesPath = public_path('certificados');
            //dd($certificatesPath);
            // Verificar se a pasta de certificados públicos existe, caso contrário, criar
            if (!file_exists($publicCertificatesPath)) {
                mkdir($publicCertificatesPath, 0755, true);
            }
            $evento = Evento::find($request->IDEvento);
            $certificadoManager = new ImageManager(new Driver());
            //dd(realpath(storage_path('app/public/modelos/'.$request->Modelo)));
           //dd($request->Tipo);
            foreach ($students as $student) {
                // Carregar o modelo de certificado
                $certificado = $certificadoManager->read(realpath(storage_path('app/public/modelos/'.$request->Modelo)));
    
                // Defina o texto com quebras de linha
                //CONTEUDO DO CERTIFICADO
                switch($request->Tipo){
                    case "Organizadores":
                        $textLines = [
                            'Confirmamos que',
                            $student->name,
                            'Concluiu participou da organização do evento '.$evento->Titulo
                        ];
                        //
                        $CDCertificado = rand(100000,999999).$student->id;
                        // Definir as propriedades da fonte
                        $fontSize = 110;
                        $lineHeight = $fontSize * 1.2; // Altura da linha
                        $x = 1600; // Posição horizontal
                        $initialY = 800; // Posição inicial vertical
                        $fontPath = public_path('fonts/arial.ttf');
                        // Desenhar cada linha de texto no certificado
                        foreach ($textLines as $index => $line) {
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
                            "Certificado" => realpath(public_path('certificados')."/".$fileName),
                            "IDInscrito" => $student->id,
                            "IDEvento" => $request->IDEvento,
                            "Codigo" => $CDCertificado
                        ]);
                    break;
                    case "Apresentadores":
                        $textLines = [
                            'Certificamos que',
                            $student->name,
                            'participaram na qualidade de APRESENTADORES, do evento '.$evento->Titulo,
                            'com a temática'.$evento->Inicio." a ".$evento->Termino
                        ];
                        //
                        $CDCertificado = rand(100000,999999).$student->id;
                        // Definir as propriedades da fonte
                        $fontSize = 110;
                        $lineHeight = $fontSize * 1.2; // Altura da linha
                        $x = 1600; // Posição horizontal
                        $initialY = 800; // Posição inicial vertical
                        $fontPath = public_path('fonts/arial.ttf');
                        // Desenhar cada linha de texto no certificado
                        foreach ($textLines as $index => $line) {
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
                            "Certificado" => realpath(public_path('certificados')."/".$fileName),
                            "IDInscrito" => $student->id,
                            "IDEvento" => $request->IDEvento,
                            "Codigo" => $CDCertificado
                        ]);
                    break;
                    case "Telespectadores":
                        $textLines = [
                            'Confirmamos que',
                            $student->name,
                            'Participou com êxito do Evento'.$evento->Titulo,
                            'Realizado de'.$evento->Inicio." a ".$evento->Termino
                        ];
                        //
                        $CDCertificado = rand(100000,999999).$student->id;
                        // Definir as propriedades da fonte
                        $fontSize = 110;
                        $lineHeight = $fontSize * 1.2; // Altura da linha
                        $x = 1600; // Posição horizontal
                        $initialY = 800; // Posição inicial vertical
                        $fontPath = public_path('fonts/arial.ttf');
                        // Desenhar cada linha de texto no certificado
                        foreach ($textLines as $index => $line) {
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
                            "Certificado" => realpath(public_path('certificados')."/".$fileName),
                            "IDInscrito" => $student->id,
                            "IDEvento" => $request->IDEvento,
                            "Codigo" => $CDCertificado
                        ]);
                    break;
                    case "Avaliador de Sessão":
                        $textLines = [
                            'Certificamos que',
                            $student->name,
                            'foi avaliador de sessões online de apresentação de trabalhos do evento'.$evento->Titulo,
                            'realizado de '.$evento->Inicio." a ".$evento->Termino
                        ];
                        //
                        $CDCertificado = rand(100000,999999).$student->id;
                        // Definir as propriedades da fonte
                        $fontSize = 110;
                        $lineHeight = $fontSize * 1.2; // Altura da linha
                        $x = 1600; // Posição horizontal
                        $initialY = 800; // Posição inicial vertical
                        $fontPath = public_path('fonts/arial.ttf');
                        // Desenhar cada linha de texto no certificado
                        foreach ($textLines as $index => $line) {
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
                            "Certificado" => realpath(public_path('certificados')."/".$fileName),
                            "IDInscrito" => $student->id,
                            "IDEvento" => $request->IDEvento,
                            "Codigo" => $CDCertificado
                        ]);
                    break;
                    case "Moderador de Sessão":
                        $textLines = [
                            'Certificamos que',
                            $student->name,
                            'foi moderador de sessões online de apresentação de trabalhos do evento'.$evento->Titulo,
                            'realizado de '.$evento->Inicio." a ".$evento->Termino
                        ];
                        //
                        $CDCertificado = rand(100000,999999).$student->id;
                        // Definir as propriedades da fonte
                        $fontSize = 110;
                        $lineHeight = $fontSize * 1.2; // Altura da linha
                        $x = 1600; // Posição horizontal
                        $initialY = 800; // Posição inicial vertical
                        $fontPath = public_path('fonts/arial.ttf');
                        // Desenhar cada linha de texto no certificado
                        foreach ($textLines as $index => $line) {
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
                            "Certificado" => realpath(public_path('certificados')."/".$fileName),
                            "IDInscrito" => $student->id,
                            "IDEvento" => $request->IDEvento,
                            "Codigo" => $CDCertificado
                        ]);
                    break;
                    case "Telespectador de Palestra":
                        foreach(DB::select("SELECT p.Titulo,MIN(p.Data) FROM telespectadores tp INNER JOIN palestras p WHERE tp.IDInscrito = $student->id GROUP BY p.Titulo") as $tel){
                            $textLines = [
                                'Certificamos que',
                                $student->name,
                                'participou da palestra de abertura do '.$evento->Titulo,
                                'sob o tema '.$tel->Titulo
                            ];
                            //
                            $CDCertificado = rand(100000,999999).$student->id.rand(1,100);
                            // Definir as propriedades da fonte
                            $fontSize = 110;
                            $lineHeight = $fontSize * 1.2; // Altura da linha
                            $x = 1600; // Posição horizontal
                            $initialY = 800; // Posição inicial vertical
                            $fontPath = public_path('fonts/arial.ttf');
                            // Desenhar cada linha de texto no certificado
                            foreach ($textLines as $index => $line) {
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
                                "Certificado" => realpath(public_path('certificados')."/".$fileName),
                                "IDInscrito" => $student->id,
                                "IDEvento" => $request->IDEvento,
                                "Codigo" => $CDCertificado
                            ]);
                        }
                    break;
                    case "Palestrantes":
                        foreach(Palestra::where('IDPalestrante',$student->id)->get() as $pal){
                            $textLines = [
                                'Certificamos que',
                                $student->name,
                                'participou, no dia'.$pal->Data.' na qualidade de palestrante,',
                                'do '.$evento->Titulo.' ministrando o tema '.$pal->Titulo
                            ];
                            //
                            $CDCertificado = rand(100000,999999).$student->id.rand(1,100);
                            // Definir as propriedades da fonte
                            $fontSize = 110;
                            $lineHeight = $fontSize * 1.2; // Altura da linha
                            $x = 1600; // Posição horizontal
                            $initialY = 800; // Posição inicial vertical
                            $fontPath = public_path('fonts/arial.ttf');
                            // Desenhar cada linha de texto no certificado
                            foreach ($textLines as $index => $line) {
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
                                "Certificado" => realpath(public_path('certificados')."/".$fileName),
                                "IDInscrito" => $student->id,
                                "IDEvento" => $request->IDEvento,
                                "Codigo" => $CDCertificado
                            ]);
                        }
                    break;
                    case "Avaliadores":
                        $textLines = [
                            'Certificamos que',
                            $student->name,
                            'foi avaliador de trabalhos escritos submetidos ao'.$evento->Titulo,
                            'realizado de '.$evento->Inicio." a ".$evento->Termino
                        ];
                        //
                        $CDCertificado = rand(100000,999999).$student->id;
                        // Definir as propriedades da fonte
                        $fontSize = 110;
                        $lineHeight = $fontSize * 1.2; // Altura da linha
                        $x = 1600; // Posição horizontal
                        $initialY = 800; // Posição inicial vertical
                        $fontPath = public_path('fonts/arial.ttf');
                        // Desenhar cada linha de texto no certificado
                        foreach ($textLines as $index => $line) {
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
                            "Certificado" => realpath(public_path('certificados')."/".$fileName),
                            "IDInscrito" => $student->id,
                            "IDEvento" => $request->IDEvento,
                            "Codigo" => $CDCertificado
                        ]);
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

    public function getCertificados(){
        $WHERE = '';
        $JOIN = '';
        $AVJOIN = 'INNER JOIN inscricoes i ON p.id = i.IDUser';
        $GROUPBY = '';
        $NONAGREGATTED = 'p.id as IDInscrito,
            CASE WHEN p.id = c.IDInscrito THEN 1 ELSE 0 END as Emitido';
        $palestrante = false;

        if (isset($_GET['Tipo'])) {
            $evento = intval($_GET['evento']); // Certifique-se de que $evento é um inteiro seguro
            $WHERE = ' WHERE i.IDEvento=' . $evento;

            if ($_GET['Tipo'] == 'Organizadores') {
                $WHERE .= ' AND p.tipo=1';
            } elseif ($_GET['Tipo'] == 'Telespectadores') {
                $WHERE .= " AND p.tipo=3 AND p.id NOT IN (SELECT IDInscrito FROM entergas INNER JOIN submissoes WHERE submissoes.IDEvento = $evento)";
            } elseif ($_GET['Tipo'] == 'Avaliador de Sessão' || $_GET['Tipo'] == 'Moderador de Sessão') {
                $WHERE .= " AND p.tipo IN (1,2) AND p.id NOT IN (SELECT IDInscrito FROM entergas INNER JOIN submissoes WHERE submissoes.IDEvento = $evento)";
            } elseif ($_GET['Tipo'] == 'Telespectador de Palestra') {
                $WHERE .= " AND p.id IN (SELECT IDInscrito FROM telespectadores INNER JOIN palestras WHERE palestras.IDEvento = $evento GROUP BY p.Titulo)";
            } elseif ($_GET['Tipo'] == 'Palestrantes') {
                $palestrante = true;
                $ev = $evento;
                $WHERE = " AND p.id IN (SELECT IDInscrito FROM telespectadores INNER JOIN palestras WHERE palestras.IDEvento = $evento)";
            } elseif ($_GET['Tipo'] == 'Avaliadores') {
                $AVJOIN = '';
                $WHERE = "WHERE p.tipo =2 AND p.id IN (SELECT IDAvaliador FROM entergas INNER JOIN submissoes WHERE submissoes.IDEvento = $evento)";
            }elseif ($_GET['Tipo'] == 'Apresentadores') {
                $GROUPBY = ' GROUP BY p.name, p.email';
                $NONAGREGATTED = 'MAX(c.Codigo) as Codigo, 
                    MIN(p.id) as IDInscrito, 
                    MAX(CASE WHEN p.id = c.IDInscrito THEN 1 ELSE 0 END) as Emitido';
                $WHERE .= " AND p.tipo=3 AND (r.Status = 'Aprovado' OR r.Status = 'Aprovado com Ressalvas')";
                $JOIN = "INNER JOIN entergas e ON p.id = e.IDInscrito ";
                $JOIN .= "INNER JOIN reprovacoes r ON e.id = r.IDEntrega ";
            }
        }

        if (!$palestrante) {
            $SQL = "SELECT 
                        p.name as Nome, 
                        p.email as Email, 
                        $NONAGREGATTED
                    FROM users p 
                    $AVJOIN
                    LEFT JOIN certificados c ON p.id = c.IDInscrito
                    $JOIN
                    $WHERE
                    $GROUPBY";
        } else {
            $SQL = "SELECT 
                        p.id as IDInscrito, 
                        p.Nome as Nome, 
                        p.Email as Email, 
                        c.Codigo, 
                        CASE WHEN p.id = c.IDInscrito THEN 1 ELSE 0 END as Emitido 
                    FROM palestrantes p 
                    LEFT JOIN certificados c ON c.IDInscrito = p.id AND p.id = $ev
                    ";
        }

        //dd($SQL);

        $registros = DB::select($SQL);

        $itensJSON = [];
        if (count($registros) > 0) {
            foreach ($registros as $r) {
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Email;
                $item[] = ($r->Emitido) ? 'Emitido' : "<input type='checkbox' name='IDParticipante[]' value='$r->IDInscrito'>";
                $item[] = ($r->Emitido) ? "<a href=" . route('Palestras/Edit', $r->IDInscrito) . ">Abrir</a>" : 'Certificado Não Emitido';
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
