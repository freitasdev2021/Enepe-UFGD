<x-educacional-layout>
    <!--ORGANIZADOR-->
    @if(in_array(Auth::user()->tipo,[1]))
    <!--NUMERO DE INSCRITOS E SUBMISSÕES-->
    <div class="row">
        <div class="col-sm-6">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1 text-white"><i class='bx bxs-user' ></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Inscritos</span>
                  <span class="info-box-number">
                  {{$Inscritos}}
                  </span>
               </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="info-box">
               <span class="info-box-icon bg-fr elevation-1 text-white"><i class='bx bxs-graduation'></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Trabalhos Submetidos</span>
                  <span class="info-box-number">
                  {{$Submissoes}}
                  </span>
               </div>
            </div>
        </div>
    </div>
    <!--SE HÁ EVENTOS CADASTRADOS-->
    @if(count($Eventos) > 0)
        @if(Session::has('IDEvento')) <!--SE O USUÁRIO ESTÁ EM UM EVENTO-->
        <div class="card">
            <div class="card-header bg-fr text-white">
                Disparar Notificações
            </div>
            <div class="card-body row">
                <form action="{{route('Notificacoes/Send')}}" method="POST">
                    @csrf
                    <div class="col-sm-12">
                        <label>Remetente</label>
                        <input type="email" class="form-control" name="Remetente" placeholder="Exemplo: remetente@ufgd.com.br" required>
                    </div>
                    <div class="col-sm-12">
                        <label>Título</label>
                        <input type="text" class="form-control" name="Titulo" placeholder="Exemplo: Aviso de Evento" required>
                    </div>
                    <div class="col-sm-12">
                        <label>Notificação</label>
                        <textarea class="form-control" name="Notificacao" placeholder="Exemplo: Novo evento a vista" required></textarea>
                    </div>
                    <br>
                    <div class="col-sm-4">
                        <button class="btn btn-success">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-header bg-fr text-white">
             Certificados
            </div>
            <div class="card-body row">
                @if(Count($Certificados))
                    @foreach($Certificados as $c)
                    <div class="card sala p-1" style="width: 18rem;">
                        <img src="{{url('storage/modelos/'.$c->Certificado)}}" class="card-img-top" alt="...">
                        <br>
                        <a href="{{url('storage/modelos/'.$c->Certificado)}}" download class="btn bg-fr text-white">Baixar</a>
                        <br>
                        <a href="{{route('Certificados/pdf',$c->Certificado)}}" download class="btn bg-fr text-white">Baixar PDF</a>
                    </div>
                    @endforeach
                @else
                <h2 align="center">Você não tem Certificados</h2>
                @endif
            </div>
         </div>
        @else <!--CASO O USUÁRIO NÃO ESTEJA EM UM EVENTO-->
            <div class="card">
                <div class="card-header bg-fr text-white">
                    Eventos
                </div>
                <div class="card-body row">
                    @foreach($Eventos as $e)
                        <form action="{{route('Eventos/Entrar')}}" method="POST" class="card sala" style="width: 18rem;">
                            @csrf
                            <img src="{{url('storage/Site/'.$e->Capa)}}" class="card-img-top" alt="...">
                            <div class="card-body">
                            <h5 class="card-title">{{$e->Evento}}</h5>
                            <p class="card-text">{{$e->Descricao}}</p>
                            @if(!$e->Inscrito && Auth::user()->tipo == 3)
                                @if($e->Inscricao != 'O prazo para a inscrição do evento está encerrado')
                                    <button class="btn bg-fr text-white disabled">Inscrições Encerradas</button>
                                @else
                                    <a href="{{route('Eventos/Inscricao',$e->IDEvento)}}" class="btn bg-fr text-white">Inscreva-se</a>
                                @endif
                            @else
                            <input type="hidden" name="IDEvento" value="{{$e->IDEvento}}">
                            <button class="btn bg-fr text-white" type="submit">Entrar</button>
                            @endif
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif <!--FIM DA VERIFICAÇÃO SE O USUÁRIO ESTÁ EM UM EVENTO-->
    @else <!--SE NÃO HÁ EVENTOS CADASTRADIS-->
    <div class="card">
        <div class="card-header bg-fr text-white">
            Cadastrar Evento
        </div>
        <div class="card-body">
            <x-cadastro-evento/>
        </div>
    </div>
    @endif <!--FIM DA VERIFICAÇÃO DE EVENTOS-->
    <!--------AVALIADOR-->
    @elseif(in_array(Auth::user()->tipo,[2]))
    @if(count($Formularios) > 0)
     <div class="card">
        <div class="card-header bg-fr text-white">
            Formulários De Avaliação Disponíveis
        </div>
        <div class="card-body row">
            <hr>
            <table class="table table-sm tabela" id="escolas">
                <thead>
                    <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Formularios as $f)
                    <tr>
                        <td>{{$f->Titulo}}</td>
                        <td><a href="{{route('Formularios/Visualizar',$f->id)}}">Abrir</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
     </div>
     @endif
     <br>
     @elseif(in_array(Auth::user()->tipo,[3]))
     @if(count($Formularios) > 0)
     <div class="card">
        <div class="card-header bg-fr text-white">
            Formulários De Avaliação Disponíveis
        </div>
        <div class="card-body row">
            <hr>
            <table class="table table-sm tabela" id="escolas">
                <thead>
                    <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Formularios as $f)
                    <tr>
                        <td>{{$f->Titulo}}</td>
                        <td><a href="{{route('Formularios/Visualizar',$f->id)}}">Abrir</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
     </div>
     @endif
     <br>
     <div class="card">
        <div class="card-header bg-fr text-white">
         Certificados
        </div>
        <div class="card-body row">
            @if(Count($Certificados))
                @foreach($Certificados as $c)
                <div class="card sala p-1" style="width: 18rem;">
                    <img src="{{asset('storage/modelos/'.$c->Certificado)}}" class="card-img-top" alt="...">
                    <br>
                    <a href="{{asset('storage/modelos/'.$c->Certificado)}}" download class="btn bg-fr text-white">Baixar</a>
                    <br>
                    <a href="{{route('Certificados/pdf',$c->Certificado)}}" download class="btn bg-fr text-white">Baixar PDF</a>
                </div>
                @endforeach
            @else
            <h2 align="center">Você não tem Certificados</h2>
            @endif
        </div>
     </div>
     @endif
  </x-educacional-layout>