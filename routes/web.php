<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\PalestrasController;
use App\Http\Controllers\AvaliadoresController;
use App\Http\Controllers\SubmissoesController;
use App\Http\Controllers\CertificadosController;
use App\Http\Controllers\SalasController;
use App\Http\Controllers\AtividadesController;
use App\Http\Controllers\InscricoesController;
use App\Http\Controllers\ZoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //EVENTOS
    Route::get('Eventos',[EventosController::class,'index'])->name('Eventos/index');
    Route::get('Eventos/list',[EventosController::class,'getEventos'])->name('Eventos/list');
    Route::get('Eventos/Cadastro',[EventosController::class,'cadastro'])->name('Eventos/Novo');
    Route::get('Eventos/Cadastro/{id}',[EventosController::class,'cadastro'])->name('Eventos/Edit');
    Route::post('Eventos/Save',[EventosController::class,'save'])->name('Eventos/Save');
    Route::post('Eventos/Delete',[EventosController::class,'delete'])->name('Eventos/Delete');
    //Eventos/Salas
    Route::get('Eventos/Salas/{IDEvento}',[SalasController::class,'index'])->name('Eventos/Salas/index');
    Route::get('Eventos/Salas/list/{IDEvento}',[SalasController::class,'getSalas'])->name('Eventos/Salas/list');
    Route::get('Eventos/{IDEvento}/Salas/Cadastro',[SalasController::class,'cadastro'])->name('Eventos/Salas/Novo');
    Route::get('Eventos/{IDEvento}/Salas/Cadastro/{id}',[SalasController::class,'cadastro'])->name('Eventos/Salas/Edit');
    Route::post('Eventos/Salas/Save',[SalasController::class,'save'])->name('Eventos/Salas/Save');
    Route::post('Eventos/Salas/Delete',[SalasController::class,'delete'])->name('Eventos/Salas/Delete');
    //Eventos/Atividades
    Route::get('Eventos/Atividades/{IDEvento}',[AtividadesController::class,'index'])->name('Eventos/Atividades/index');
    Route::get('Eventos/Atividades/list/{IDEvento}',[AtividadesController::class,'getAtividades'])->name('Eventos/Atividades/list');
    Route::get('Eventos/{IDEvento}/Atividades/Cadastro',[AtividadesController::class,'cadastro'])->name('Eventos/Atividades/Novo');
    Route::get('Eventos/{IDEvento}/Atividades/Cadastro/{id}',[AtividadesController::class,'cadastro'])->name('Eventos/Atividades/Edit');
    Route::post('Eventos/Atividades/Save',[AtividadesController::class,'save'])->name('Eventos/Atividades/Save');
    Route::post('Eventos/Atividades/Delete',[AtividadesController::class,'delete'])->name('Eventos/Atividades/Delete');
    //Atividades
    Route::get('Atividades/list',[AtividadesController::class,'getAtividades'])->name('Atividades/list');
    Route::get('Atividades',[AtividadesController::class,'indexInscrito'])->name('Atividades/index');
    Route::get('Atividades/Atividade/{IDAtividade}',[AtividadesController::class,'atividade'])->name('Atividades/Atividade');
    //Palestras
    Route::get('Palestras', [PalestrasController::class, 'index'])->name('Palestras/index');
    Route::get('Palestras/list', [PalestrasController::class, 'getPalestras'])->name('Palestras/list');
    Route::get('Palestras/Cadastro', [PalestrasController::class, 'cadastro'])->name('Palestras/Novo');
    Route::get('Palestras/Cadastro/{id}', [PalestrasController::class, 'cadastro'])->name('Palestras/Edit');
    Route::post('Palestras/Save', [PalestrasController::class, 'save'])->name('Palestras/Save');
    Route::post('Palestras/Delete', [PalestrasController::class, 'delete'])->name('Palestras/Delete');
    //Palestrantes
    Route::get('Palestrantes', [PalestrasController::class, 'indexPalestrantes'])->name('Palestrantes/index');
    Route::get('Palestrantes/list', [PalestrasController::class, 'getPalestrantes'])->name('Palestrantes/list');
    Route::get('Palestrantes/Cadastro', [PalestrasController::class, 'cadastroPalestrantes'])->name('Palestrantes/Novo');
    Route::get('Palestrantes/Cadastro/{id}', [PalestrasController::class, 'cadastroPalestrantes'])->name('Palestrantes/Edit');
    Route::post('Palestrantes/Save', [PalestrasController::class, 'savePalestrantes'])->name('Palestrantes/Save');
    Route::post('Palestrantes/Delete', [PalestrasController::class, 'deletePalestrantes'])->name('Palestrantes/Delete');
    //Avaliadores
    Route::get('Avaliadores', [AvaliadoresController::class, 'index'])->name('Avaliadores/index');
    Route::get('Avaliadores/list', [AvaliadoresController::class, 'getAvaliadores'])->name('Avaliadores/list');
    Route::get('Avaliadores/Cadastro', [AvaliadoresController::class, 'cadastro'])->name('Avaliadores/Novo');
    Route::get('Avaliadores/Cadastro/{id}', [AvaliadoresController::class, 'cadastro'])->name('Avaliadores/Edit');
    Route::post('Avaliadores/Save', [AvaliadoresController::class, 'save'])->name('Avaliadores/Save');
    Route::post('Avaliadores/Delete', [AvaliadoresController::class, 'delete'])->name('Avaliadores/Delete');
    //Submissões
    Route::get('Submissoes', [SubmissoesController::class, 'index'])->name('Submissoes/index');
    Route::get('Submissoes/Cadastro', [SubmissoesController::class, 'cadastro'])->name('Submissoes/Novo');
    Route::get('Submissoes/Cadastro/{id}', [SubmissoesController::class, 'cadastro'])->name('Submissoes/Edit');
    Route::get('Submissoes/Correcao/{IDEntrega}', [SubmissoesController::class, 'correcao'])->name('Submissoes/Correcao');
    Route::get('Submissoes/getTrabalho/{IDEntrega}', [SubmissoesController::class, 'getTrabalho'])->name('Submissoes/getTrabalho');
    Route::get('Submissoes/Entregues/{IDSubmissao}', [SubmissoesController::class, 'entregues'])->name('Submissoes/Entregues');
    Route::get('Submissoes/Entregues/list/{IDSubmissao}', [SubmissoesController::class, 'getEntregues'])->name('Submissoes/Entregues/list');
    Route::post('Submissoes/Save', [SubmissoesController::class, 'save'])->name('Submissoes/Save');
    Route::post('Submissoes/Delete', [SubmissoesController::class, 'delete'])->name('Submissoes/Delete');
    Route::get('Submissoes/list', [SubmissoesController::class, 'getSubmissoes'])->name('Submissoes/list');
    Route::get('Submissoes/Entrega/{IDSubmissao}', [SubmissoesController::class, 'entrega'])->name('Submissoes/Entrega');
    Route::post('Submissoes/Entregas/Save', [SubmissoesController::class, 'saveEntrega'])->name('Submissoes/Entregas/Save');
    Route::post('Submissoes/Corrigir', [SubmissoesController::class, 'corrigir'])->name('Submissoes/Corrigir');
    Route::post('Submissoes/Entregues/setAvaliador', [SubmissoesController::class, 'setAvaliador'])->name('Submissoes/Entregues/setAvaliador');
    //Certificados
    Route::get('Certificados', [CertificadosController::class, 'index'])->name('Certificados/index');
    Route::get('Certificados/Modelos', [CertificadosController::class, 'modelos'])->name('Certificados/Modelos');
    Route::get('Certificados/Modelos/Cadastro', [CertificadosController::class, 'cadastroModelos'])->name('Certificados/Modelos/Novo');
    Route::post('Certificados/Save', [CertificadosController::class, 'save'])->name('Certificados/Save');
    Route::post('Certificados/Delete', [CertificadosController::class, 'delete'])->name('Certificados/Delete');
    Route::post('Certificados/Modelos/Save', [CertificadosController::class, 'saveModelo'])->name('Certificados/Modelos/Save');
    Route::get('Certificados/list', [CertificadosController::class, 'getCertificados'])->name('Certificados/list');
    //Inscrições
    Route::post('Eventos/Inscrever',[EventosController::class,'inscrever'])->name('Eventos/Inscrever');
    Route::post('Eventos/saveInscricaoAluno',[EventosController::class,'saveInscricaoAluno'])->name('Eventos/saveInscricaoAluno');
    Route::get('Eventos/Inscricoes/inscreverAluno/{IDEvento}',[EventosController::class,'inscreverAluno'])->name('Eventos/Inscricoes/inscreverAluno');
    Route::get('Eventos/Inscricoes/{IDEvento}',[EventosController::class,'inscricoes'])->name('Eventos/Inscricoes');
    Route::get('Eventos/Inscricoes/list/{IDEvento}',[EventosController::class,'getInscricoes'])->name('Eventos/Inscricoes/list');
    Route::post('Eventos/Entrar',[EventosController::class,'entrar'])->name('Eventos/Entrar');
    Route::post('Eventos/inscreverAluno',[EventosController::class,'inscreverAluno'])->name('Eventos/inscreverAluno');
    Route::get('Eventos/Inscricao/{IDEvento}', [EventosController::class, 'inscricao'])->name('Eventos/Inscricao');
    //
    // Rota para gerar a assinatura para ingressar na reunião
    Route::get('/zoom/signature', [AtividadesController::class, 'generateZoomSignature'])->name('zoom.signature');
    //
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
