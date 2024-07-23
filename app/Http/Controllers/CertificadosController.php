<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Modelo;
use App\Models\User;
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
            $students = User::whereIn('id', $studentIds)->get();
            $certificatesPath = storage_path('app/public/modelos');
            $publicCertificatesPath = public_path('certificados');
            //dd($certificatesPath);
            // Verificar se a pasta de certificados públicos existe, caso contrário, criar
            if (!file_exists($publicCertificatesPath)) {
                mkdir($publicCertificatesPath, 0755, true);
            }

            $certificadoManager = new ImageManager(new Driver());
            //dd(realpath(storage_path('app/public/modelos/'.$request->Modelo)));
           
            foreach ($students as $student) {
                // Carregar o modelo de certificado
                $certificado = $certificadoManager->read(realpath(storage_path('app/public/modelos/'.$request->Modelo)));
    
                // Defina o texto com quebras de linha
                $textLines = [
                    'Certificamos que',
                    $student->name,
                    'concluiu o curso XXXX'
                ];

                // Definir as propriedades da fonte
                $fontSize = 130;
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
                $fileName = 'certificado_' . $student->id . '.jpg';
    
                // Salvar o certificado na pasta de armazenamento
                $certificado->save($certificatesPath . '/' . $fileName);
    
                // Copiar o certificado para a pasta pública
                copy($certificatesPath . '/' . $fileName, $publicCertificatesPath . '/' . $fileName);
    
                // Atualizar o registro do aluno com o caminho do certificado público
                // $student->certificate_path = 'certificados/' . $fileName;
                // $student->save();
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

    public function getCertificados(){
        $WHERE =  '';
        $JOIN = '';
        $GROUPBY = '';
        $NONAGREGATTED = 'p.id as IDInscrito,
                CASE WHEN p.id = c.IDInscrito THEN 1 ELSE 0 END as Emitido';
        if(isset($_GET['Tipo'])){
            $WHERE = ' WHERE i.IDEvento='.$_GET['evento'];
            $evento = $_GET['evento'];
            if($_GET['Tipo'] == 'Organizadores'){
                $WHERE .= ' AND p.tipo=1';
            }elseif($_GET['Tipo'] == 'Telespectadores'){
                $WHERE .= " AND p.tipo=3 AND p.id NOT IN(SELECT IDInscrito FROM entergas INNER JOIN submissoes WHERE submissoes.IDEvento = $evento)";
            }elseif($_GET['Tipo'] == 'Apresentadores'){
                $GROUPBY = ' GROUP BY p.name,p.email';
                $NONAGREGATTED = 'MIN(p.id) as IDInscrito,
                MAX(CASE WHEN p.id = c.IDInscrito THEN 1 ELSE 0 END) as Emitido';
                $WHERE .= " AND p.tipo=3 AND r.Status = 'Aprovado' OR r.Status = 'Aprovado com Ressalvas' ";
                $JOIN = "INNER JOIN entergas e ON(p.id = e.IDInscrito)";
                $JOIN .= "INNER JOIN reprovacoes r ON(e.id = r.IDEntrega)";
            }
        }
        $SQL = "SELECT 
                p.name as Nome, 
                p.email as Email,
                $NONAGREGATTED
            FROM users p 
            INNER JOIN inscricoes i ON(p.id = i.IDUser)
            LEFT JOIN certificados c ON(p.id = c.IDInscrito)
            $JOIN
            $WHERE
            $GROUPBY
        ";
        $registros = DB::select($SQL);
        //dd($registros);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Email;
                $item[] = ($r->Emitido) ? 'Emitido' : "<input type='checkbox' name='IDParticipante[]' value='$r->IDInscrito'>";
                $item[] = ($r->Emitido) ? "<a href=".route('Palestras/Edit',$r->IDInscrito).">Abrir</a>" : 'Certificado Não Emitido';
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

    public function cadastroModelos(){
        return view('Certificados.cadastro');
    }
}
