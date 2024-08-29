<x-educacional-layout>
    <!--ORGANIZADOR-->
    @if(in_array(Auth::user()->tipo,[1]))
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
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Eventos/Save')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("POST")
                    @if(session('success'))
                    <div class="col-sm-12 shadow p-2 bg-success text-white">
                        <strong>{{session('success')}}</strong>
                    </div>
                    @elseif(session('error'))
                    <div class="col-sm-12 shadow p-2 bg-danger text-white">
                        <strong>{{session('error')}}</strong>
                    </div>
                    <br>
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Capa do Evento</label>
                            <img src="{{asset('img/uploadModelo.jpg')}}" id="capa" height="500px" width="100%">
                            <input type="file" name="Capa" style="display:none;" onchange="displaySelectedImage(event, 'capa')">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Título</label>
                            <input type="text" name="Titulo" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início</label>
                            <input type="datetime-local" name="Inicio" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label>Término</label>
                            <input type="datetime-local" name="Termino" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início das Inscrições</label>
                            <input type="date" name="INIInscricao" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label>Término das Inscrições</label>
                            <input type="date" name="TERInscricoes" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início das Submissões</label>
                            <input type="datetime-local" name="INISubmissao" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label>Término das Submissões</label>
                            <input type="datetime-local" name="TERSubmissao" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Normas de Apresentação</label>
                            <textarea name="Normas" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição</label>
                            <textarea name="Descricao" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Modelo de Apresentação</label>
                            <input type="file" name="ModeloApresentacao" class="form-control">
                            <input type="hidden" name="oldModeloApresentacao">
                        </div>
                        <div class="col-sm-12">
                            <label>Tipos de Atividades</label>
                            <input type="text" name="TPAtividade" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label>Personalização do Site</label>
                        <div>
                            <input type="checkbox" name="Site[]" value="Capa">&nbsp;Capa
                            <input type="checkbox" name="Site[]" value="Inscritos">&nbsp;Inscricoes
                            <input type="checkbox" name="Site[]" value="Submissoes">&nbsp;Submissoes
                            <input type="checkbox" name="Site[]" value="Normas">&nbsp;Normas
                            <input type="checkbox" name="Site[]" value="Palestras">&nbsp;Atividades
                            <input type="checkbox" name="Site[]" value="Contatos">&nbsp;Contatos
                            <input type="checkbox" name="Site[]" value="Prazo de Submissoes">&nbsp;Prazo de Submissões
                            <input type="checkbox" name="Site[]" value="Prazo de Inscricoes">&nbsp;Prazo de Inscrições
                            <input type="checkbox" name="Site[]" value="Inicio e Termino do Evento">&nbsp;Início e Término do Evento
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <button class="btn btn-fr text-white col-sm-12" id="adicionarContato" type="button">Adicionar Contato</button>
                        </div>
                        <!--REGISTRO DOS CONTATOS-->
                        <div class="row contatos">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <button class="btn btn-fr text-white col-sm-12" id="adicionarCategoria" type="button">Adicionar Categoria</button>
                        </div>
                        <!--REGISTRO DE CATEGORIAS-->
                        <div class="row categorias">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <button class="btn btn-fr text-white col-sm-12" id="adicionarModalidade" type="button">Adicionar Modalidade</button>
                        </div>
                        <!--REGISTRO DE MODALIDADES-->
                        <div class="row modalidades">
                            
                        </div>
                    </div>
                    <!--MODELO MODALIDADES-->
                    <div class="modalidade" style="display:none;">
                        <div class="row mdd">
                            <div class="col-sm-10">
                                <label>Nome</label>
                                <input type="text" name="Modalidade[]" class="form-control" value="">
                            </div>
                            <div class="col-sm-2">
                                <label>Remover</label>
                                <input type="button" id="removeModalidade" class="form-control btn btn-danger" value="X">
                            </div>
                        </div>
                    </div>
                    <!--MODELO CONTATOS-->
                    <div class="contato" style="display:none;">
                        <div class="row ctt">
                            <div class="col-sm-6">
                                <label>Nome</label>
                                <input type="text" name="Nome[]" class="form-control" value="">
                            </div>
                            <div class="col-sm-4">
                                <label>Contato</label>
                                <input type="text" name="Contato[]" class="form-control" value="">
                            </div>
                            <div class="col-sm-2">
                                <label>Remover</label>
                                <input type="button" id="removeContato" class="form-control btn btn-danger" value="X">
                            </div>
                        </div>
                    </div>
                    <!--MODELO CATEGORIAS-->
                    <div class="categoria" style="display:none;">
                        <div class="row ctg">
                            <div class="col-sm-10">
                                <label>Nome</label>
                                <input type="text" name="Categoria[]" class="form-control" value="">
                            </div>
                            <div class="col-sm-2">
                                <label>Remover</label>
                                <input type="button" id="removeCategoria" class="form-control btn btn-danger" value="X">
                            </div>
                        </div>
                    </div>
                    <!--PERSONALIZAÇÃO DO SITE-->
                    <!-------------------------->
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Eventos/index')}}">Voltar</a>
                    </div>
                </form>
            </div>
            <script>
                $("#capa").on("click",function(){
                    $("input[name=Capa]").trigger("click")
                })
                //
                function displaySelectedImage(event, elementId) {
                    const selectedImage = document.getElementById(elementId);
                    const fileInput = event.target;
            
                    if (fileInput.files && fileInput.files[0]) {
                        const reader = new FileReader();
            
                        reader.onload = function(e) {
                            selectedImage.src = e.target.result;
                        };
            
                        reader.readAsDataURL(fileInput.files[0]);
                    }
                }
                //CONTATOS
                $('#adicionarContato').click(function() {
                    $('.contatos').append($(".contato").html());
                });
                //
                $(".contatos").on("click","#removeContato",function(){
                    $(this).parents(".ctt").remove()
                })
                //CATEGORIAS
                $('#adicionarCategoria').click(function() {
                    $('.categorias').append($(".categoria").html());
                });
                //
                $(".categorias").on("click","#removeCategoria",function(){
                    $(this).parents(".ctg").remove()
                })
                //MODALIDADES
                $('#adicionarModalidade').click(function() {
                    $('.modalidades').append($(".modalidade").html());
                });
                //
                $(".modalidades").on("click","#removeModalidade",function(){
                    $(this).parents(".mdd").remove()
                })
                //
            </script>
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