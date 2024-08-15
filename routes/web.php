<?php

use App\Http\Controllers\DyteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\PalestrasController;
use App\Http\Controllers\AvaliadoresController;
use App\Http\Controllers\SubmissoesController;
use App\Http\Controllers\OrganizadoresController;
use App\Http\Controllers\CertificadosController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\NotificaController;
use App\Http\Controllers\SuporteController;
use App\Models\Certificados;
use App\Models\Formulario;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SalasController;
use App\Http\Controllers\AtividadesController;
use App\Http\Controllers\FormulariosController;
use App\Http\Controllers\ZoomController;
use Illuminate\Support\Facades\Route;
//ROTAS PRINCIPAIS
Route::get('/', function () {
    return view('auth.register');
});
Route::get('/dashboard', function () {
    $Formularios = [];

    if(Session::get('IDEvento')){
        $Formularios = Formulario::where('IDEvento',Session::get('IDEvento'))->get();
    }

    return view('dashboard',[
        "Certificados" => Certificados::where("IDInscrito",Auth::user()->id)->get(),
        "Formularios" => $Formularios
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');
//ROTA DO SITE
Route::get('Evento/{id}',[SiteController::class,'site'])->name("Site");
Route::post('Certificados/Validar', [CertificadosController::class, 'validarCertificados'])->name('Certificados/Validar');
//ROTA DO USUARIO LOGADO
Route::middleware('auth')->group(function () {
    //CAMADA DE PROTEÇÃO GERAL
    Route::middleware('geral')->group(function(){
        //SUPORTE
        Route::get('Suporte',[SuporteController::class,'index'])->name('Suporte/index');
        Route::get('Suporte/Cadastro',[SuporteController::class,'cadastro'])->name('Suporte/Novo');
        Route::get('Suporte/Cadastro/{id}',[SuporteController::class,'cadastro'])->name('Suporte/Edit');
        Route::get('Suporte/Receber/{id}',[SuporteController::class,'receber'])->name('Suporte/Receber');
        Route::get('Suporte/list', [SuporteController::class, 'getSuportes'])->name('Suporte/list');
        Route::post('Suporte/Save', [SuporteController::class, 'save'])->name('Suporte/Save');
        Route::post('Suporte/Enviar', [SuporteController::class, 'sendMessage'])->name('Suporte/Enviar');
        //PERFIL
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        //ATIVIDADES
        Route::get('/Atividades/Abrir',[DyteController::class,'abrirSala']);
        //SUBMISSOES
        Route::get('Submissoes/Entrega/{IDSubmissao}/{IDEntrega}', [SubmissoesController::class, 'entrega'])->name('Submissoes/Entrega');
        Route::post('Submissoes/Entregas/Save', [SubmissoesController::class, 'saveEntrega'])->name('Submissoes/Entregas/Save');
        Route::get('Submissoes/getTrabalho/{IDEntrega}', [SubmissoesController::class, 'getTrabalho'])->name('Submissoes/getTrabalho');
        //EVENTOS
        Route::get('Formularios/Visualizar/{id}',[FormulariosController::class,'visualizar'])->name('Formularios/Visualizar');
        Route::get('Eventos/Cadastro/{id}',[EventosController::class,'cadastro'])->name('Eventos/Edit');
        Route::post('Formularios/Responder',[FormulariosController::class,'responder'])->name('Formularios/Responder');
        Route::get('Eventos',[EventosController::class,'index'])->name('Eventos/index');
        Route::get('Eventos/list',[EventosController::class,'getEventos'])->name('Eventos/list');
        Route::get('Atividades',[AtividadesController::class,'indexInscrito'])->name('Atividades/index');
        Route::get('Atividades/Atividade/{IDAtividade}',[AtividadesController::class,'atividade'])->name('Atividades/Atividade');
        Route::get('Palestras', [PalestrasController::class, 'index'])->name('Palestras/index');
        Route::get('Palestras/list', [PalestrasController::class, 'getPalestras'])->name('Palestras/list');
        Route::get('Palestrantes', [PalestrasController::class, 'indexPalestrantes'])->name('Palestrantes/index');
        Route::get('Palestrantes/list', [PalestrasController::class, 'getPalestrantes'])->name('Palestrantes/list');
        Route::get('Submissoes', [SubmissoesController::class, 'index'])->name('Submissoes/index');
    });
    //CAMADA DE PROTEÇÃO ORGANIZADORES
    Route::middleware('organizador')->group(function(){
        //FORMULÁRIO
        Route::post('Formularios/Save',[FormulariosController::class,'save'])->name('Formularios/Save');
        Route::get('Formularios',[FormulariosController::class,'index'])->name('Formularios/index');
        Route::get('Formularios/list',[FormulariosController::class,'getFormularios'])->name('Formularios/list');
        Route::get('Formularios/Cadastro',[FormulariosController::class,'cadastro'])->name('Formularios/Novo');
        Route::get('Formularios/Cadastro/{id}',[FormulariosController::class,'cadastro'])->name('Formularios/Edit');
        //Formulario
        Route::get('Formularios/Respostas/{id}',[FormulariosController::class,'respostas'])->name('Formularios/Respostas');
        Route::get('Formularios/getRespostas/{id}',[FormulariosController::class,'getRespostas'])->name('Formularios/getRespostas');
        Route::get('Formularios/Respostas/Export/{id}', [ExportController::class, 'exportRespostas'])->name('Formularios/Respostas/Export');

        //ORGANIZADORES
        Route::post('Organizadores/Save',[OrganizadoresController::class,'save'])->name('Organizadores/Save');
        Route::get('Organizadores',[OrganizadoresController::class,'index'])->name('Organizadores/index');
        Route::get('Organizadores/list',[OrganizadoresController::class,'getOrganizadores'])->name('Organizadores/list');
        Route::get('Organizadores/Cadastro',[OrganizadoresController::class,'cadastro'])->name('Organizadores/Novo');
        Route::get('Organizadores/Cadastro/{id}',[OrganizadoresController::class,'cadastro'])->name('Organizadores/Edit');
        //EVENTOS
        Route::post('Eventos/Save',[EventosController::class,'save'])->name('Eventos/Save');
        Route::post('Eventos/Delete',[EventosController::class,'delete'])->name('Eventos/Delete');
        Route::get('Eventos/Cadastro',[EventosController::class,'cadastro'])->name('Eventos/Novo');
        Route::get('Eventos/Salas/{IDEvento}',[SalasController::class,'index'])->name('Eventos/Salas/index');
        Route::get('Eventos/Salas/list/{IDEvento}',[SalasController::class,'getSalas'])->name('Eventos/Salas/list');
        Route::get('Eventos/{IDEvento}/Salas/Cadastro',[SalasController::class,'cadastro'])->name('Eventos/Salas/Novo');
        Route::get('Eventos/{IDEvento}/Salas/Cadastro/{id}',[SalasController::class,'cadastro'])->name('Eventos/Salas/Edit');
        //SALAS
        Route::post('Eventos/Salas/Save',[SalasController::class,'save'])->name('Eventos/Salas/Save');
        Route::post('Eventos/Salas/Delete',[SalasController::class,'delete'])->name('Eventos/Salas/Delete');
        //ATIVIDADES
        Route::post('Eventos/Atividades/Save',[AtividadesController::class,'save'])->name('Eventos/Atividades/Save');
        Route::post('Eventos/Atividades/Delete',[AtividadesController::class,'delete'])->name('Eventos/Atividades/Delete');
        Route::get('Eventos/Atividades/{IDEvento}',[AtividadesController::class,'index'])->name('Eventos/Atividades/index');
        Route::get('Eventos/Atividades/list/{IDEvento}',[AtividadesController::class,'getAtividades'])->name('Eventos/Atividades/list');
        Route::get('Eventos/{IDEvento}/Atividades/Cadastro',[AtividadesController::class,'cadastro'])->name('Eventos/Atividades/Novo');
        Route::get('Eventos/{IDEvento}/Atividades/Cadastro/{id}',[AtividadesController::class,'cadastro'])->name('Eventos/Atividades/Edit');
        Route::get('Atividades/list',[AtividadesController::class,'getAtividades'])->name('Atividades/list');
        //SITE
        Route::get('Site',[SiteController::class,'index'])->name('Site/index');
        //PALESTRAS
        Route::post('Palestras/Save', [PalestrasController::class, 'save'])->name('Palestras/Save');
        Route::post('Palestras/Delete', [PalestrasController::class, 'delete'])->name('Palestras/Delete');
        Route::post('Palestras/Presenca', [PalestrasController::class, 'presenca'])->name('Palestras/Presenca');
        Route::get('Palestras/Participantes/list/{IDPalestra}', [PalestrasController::class, 'getParticipantesPalestras'])->name('Palestras/Participantes/list');
        Route::get('Palestras/Participantes/{IDPalestra}', [PalestrasController::class, 'telespectadores'])->name('Palestras/Participantes');
        Route::get('Palestras/Cadastro', [PalestrasController::class, 'cadastro'])->name('Palestras/Novo');
        Route::get('Palestras/Cadastro/{id}', [PalestrasController::class, 'cadastro'])->name('Palestras/Edit');
        //PALESTRANTES
        Route::get('Palestrantes/Cadastro', [PalestrasController::class, 'cadastroPalestrantes'])->name('Palestrantes/Novo');
        Route::get('Palestrantes/Cadastro/{id}', [PalestrasController::class, 'cadastroPalestrantes'])->name('Palestrantes/Edit');
        Route::post('Palestrantes/Save', [PalestrasController::class, 'savePalestrantes'])->name('Palestrantes/Save');
        Route::post('Palestrantes/Delete', [PalestrasController::class, 'deletePalestrantes'])->name('Palestrantes/Delete');
        //AVALIADORES
        Route::get('Avaliadores', [AvaliadoresController::class, 'index'])->name('Avaliadores/index');
        Route::get('Avaliadores/list', [AvaliadoresController::class, 'getAvaliadores'])->name('Avaliadores/list');
        Route::get('Avaliadores/Cadastro', [AvaliadoresController::class, 'cadastro'])->name('Avaliadores/Novo');
        Route::get('Avaliadores/Cadastro/{id}', [AvaliadoresController::class, 'cadastro'])->name('Avaliadores/Edit');
        Route::post('Avaliadores/Save', [AvaliadoresController::class, 'save'])->name('Avaliadores/Save');
        Route::post('Avaliadores/Delete', [AvaliadoresController::class, 'delete'])->name('Avaliadores/Delete');
        //SUBMISSOES
        Route::get('Submissoes/RemoveAtr/{id}', [SubmissoesController::class, 'RemoveAtr'])->name('Submissoes/RemoveAtr');
        Route::get('Submissoes/Cadastro', [SubmissoesController::class, 'cadastro'])->name('Submissoes/Novo');
        Route::get('Submissoes/Cadastro/{id}', [SubmissoesController::class, 'cadastro'])->name('Submissoes/Edit');
        Route::get('Submissoes/Entregues/{IDSubmissao}', [SubmissoesController::class, 'entregues'])->name('Submissoes/Entregues');
        Route::get('Submissoes/Entregues/list/{IDSubmissao}', [SubmissoesController::class, 'getEntregues'])->name('Submissoes/Entregues/list');
        Route::post('Submissoes/Save', [SubmissoesController::class, 'save'])->name('Submissoes/Save');
        Route::post('Submissoes/Delete', [SubmissoesController::class, 'delete'])->name('Submissoes/Delete');
        Route::post('Submissoes/Entregues/setAvaliador', [SubmissoesController::class, 'setAvaliador'])->name('Submissoes/Entregues/setAvaliador');
        //CERTIFICADOS
        Route::get('Modelos/Excluir/{id}', [CertificadosController::class, 'delete'])->name('Modelos/Excluir');
        Route::post('Certificados/Modelos/Save', [CertificadosController::class, 'saveModelo'])->name('Certificados/Modelos/Save');
        Route::get('Certificados/list', [CertificadosController::class, 'getCertificados'])->name('Certificados/list');
        Route::get('Certifica', [CertificadosController::class, 'index'])->name('Certifica/index');
        Route::get('Certificados/Modelos', [CertificadosController::class, 'modelos'])->name('Certificados/Modelos');
        Route::get('Certificados/Modelos/Cadastro', [CertificadosController::class, 'cadastroModelos'])->name('Certificados/Modelos/Novo');
        Route::post('Certificados/Save', [CertificadosController::class, 'gerarCertificados'])->name('Certificados/Save');
        Route::post('Eventos/saveInscricaoAluno',[EventosController::class,'saveInscricaoAluno'])->name('Eventos/saveInscricaoAluno');
        //INSCRIÇÕES
        Route::get('Eventos/Inscricoes/inscreverAluno/{IDEvento}/{IDAluno}',[EventosController::class,'inscreverAluno'])->name('Eventos/Inscricoes/editarAluno');
        Route::get('Eventos/Inscricoes/inscreverAluno/{IDEvento}',[EventosController::class,'inscreverAluno'])->name('Eventos/Inscricoes/inscreverAluno');
        Route::get('Eventos/Inscricoes/{IDEvento}',[EventosController::class,'inscricoes'])->name('Eventos/Inscricoes');
        Route::get('Eventos/Inscricoes/list/{IDEvento}',[EventosController::class,'getInscricoes'])->name('Eventos/Inscricoes/list');
        //Notificações
        Route::post('Notificacoes',[NotificaController::class,'notificacoes'])->name('Notificacoes/Send');
        //
    });
    //CAMADA DE PROTEÇÃO AVALIADORES
    Route::middleware('avaliador')->group(function(){
        
    });
    //CAMADA DE PROTEÇÃO INSCRITOS
    Route::middleware('participante')->group(function(){
        Route::post('Eventos/Entrar',[EventosController::class,'entrar'])->name('Eventos/Entrar');
        Route::post('Eventos/Inscrever',[EventosController::class,'inscrever'])->name('Eventos/Inscrever');
        Route::get('Eventos/Inscricao/{IDEvento}', [EventosController::class, 'inscricao'])->name('Eventos/Inscricao');
    });
    //CAMADA DE PROTEÇÃO BANCA
    Route::middleware('banca')->group(function(){
        Route::get('Submissoes/Correcao/{IDEntrega}', [SubmissoesController::class, 'correcao'])->name('Submissoes/Correcao');
        Route::post('Submissoes/Corrigir', [SubmissoesController::class, 'corrigir'])->name('Submissoes/Corrigir');
        Route::get('Submissoes/list', [SubmissoesController::class, 'getSubmissoes'])->name('Submissoes/list');
    });
    //Submissões
    //Route::post('Eventos/inscreverAluno',[EventosController::class,'inscreverAluno'])->name('Eventos/inscreverAluno');
    //
    // Rota para gerar a assinatura para ingressar na reunião
    //Route::get('/zoom/signature', [AtividadesController::class, 'generateZoomSignature'])->name('zoom.signature');
    //
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
