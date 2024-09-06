<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
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
                    @if(isset($Registro))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Capa do Evento</label>
                            <img src="{{isset($Registro) ? url('storage/Site/'.$Registro->Capa) : asset('img/uploadModelo.jpg') }}" id="capa" height="500px" width="100%">
                            <input type="file" name="Capa" style="display:none;" onchange="displaySelectedImage(event, 'capa') {{!isset($Registro) ? 'required' : ''}}">
                            <input type="hidden" name="oldCapa" value="{{isset($Registro) ? $Registro->Capa : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Título</label>
                            <input type="text" name="Titulo" class="form-control" value="{{isset($Registro) ? $Registro->Titulo : ''}}" required maxlength="250">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início</label>
                            <input type="datetime-local" name="Inicio" class="form-control" value="{{(isset($Registro)) ? $Registro->Inicio : ''}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Término</label>
                            <input type="datetime-local" name="Termino" class="form-control" value="{{(isset($Registro)) ? $Registro->Termino : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início das Inscrições</label>
                            <input type="date" name="INIInscricao" class="form-control" value="{{(isset($Registro)) ? $Registro->INIInscricao : ''}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Término das Inscrições</label>
                            <input type="date" name="TERInscricoes" class="form-control" value="{{(isset($Registro)) ? $Registro->TERInscricoes : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início das Submissões</label>
                            <input type="datetime-local" name="INISubmissao" class="form-control" value="{{(isset($Registro)) ? $Registro->INISubmissao : ''}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Término das Submissões</label>
                            <input type="datetime-local" name="TERSubmissao" class="form-control" value="{{(isset($Registro)) ? $Registro->TERSubmissao : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Normas de Apresentação</label>
                            <textarea name="Normas" class="form-control" required>{{isset($Registro) ? $Registro->Normas : ''}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição</label>
                            <textarea name="Descricao" class="form-control" required>{{isset($Registro) ? $Registro->Descricao : ''}}</textarea>
                        </div>
                    </div>
                    <input type="hidden" name="Ensalamento">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Modelo de Apresentação</label>
                            <input type="file" name="ModeloApresentacao" class="form-control" {{!isset($Registro) ? 'required' : ''}}>
                            <input type="hidden" name="oldModeloApresentacao" value="{{isset($Registro) ? $Registro->ModeloApresentacao : ''}}">
                        </div>
                        <div class="col-sm-12">
                            <label>Tipos de Atividades</label>
                            <input type="text" name="TPAtividade" class="form-control" maxlength="45" value="{{isset($Registro) ? $Registro->TPAtividade : ''}}" required>
                            <input type="hidden" name="oldModeloApresentacao" value="{{isset($Registro) ? $Registro->ModeloApresentacao : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label>Personalização do Site</label>
                        <div>
                            <input type="checkbox" name="Site[]" value="Capa" {{ isset($Registro) && in_array("Capa",$Site) ? 'checked' : ''}}>&nbsp;Capa
                            <input type="checkbox" name="Site[]" value="Inscritos" {{ isset($Registro) && in_array("Inscritos",$Site) ? 'checked' : ''}}>&nbsp;Inscricoes
                            <input type="checkbox" name="Site[]" value="Submissoes" {{ isset($Registro) && in_array("Submissoes",$Site) ? 'checked' : ''}}>&nbsp;Submissoes
                            <input type="checkbox" name="Site[]" value="Normas" {{ isset($Registro) && in_array("Normas",$Site) ? 'checked' : ''}}>&nbsp;Normas
                            <input type="checkbox" name="Site[]" value="Palestras" {{ isset($Registro) && in_array("Palestras",$Site) ? 'checked' : ''}}>&nbsp;Palestras
                            <input type="checkbox" name="Site[]" value="Contatos" {{ isset($Registro) && in_array("Contatos",$Site) ? 'checked' : ''}}>&nbsp;Contatos
                            <input type="checkbox" name="Site[]" value="Prazo de Submissoes" {{ isset($Registro) && in_array("Prazo de Submissoes",$Site) ? 'checked' : ''}}>&nbsp;Prazo de Submissões
                            <input type="checkbox" name="Site[]" value="Prazo de Inscricoes" {{ isset($Registro) && in_array("Prazo de Inscricoes",$Site) ? 'checked' : ''}}>&nbsp;Prazo de Inscrições
                            <input type="checkbox" name="Site[]" value="Inicio e Termino do Evento" {{ isset($Registro) && in_array("Inicio e Termino do Evento",$Site) ? 'checked' : ''}}>&nbsp;Início e Término do Evento
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <button class="btn btn-fr text-white col-sm-12" id="adicionarContato" type="button">Adicionar Contato</button>
                        </div>
                        <!--REGISTRO DOS CONTATOS-->
                        <div class="row contatos">
                            @if(isset($Registro) && count($Contatos) > 0)
                                @foreach($Contatos as $cKey => $c)
                                    @if(!empty($cKey))
                                        <div class="row ctt">
                                            <div class="col-sm-6">
                                                <label>Nome</label>
                                                <input type="text" name="Nome[]" class="form-control" value="{{$cKey}}">
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Contato</label>
                                                <input type="text" name="Contato[]" class="form-control" value="{{$c}}" >
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Remover</label>
                                                <input type="button" id="removeContato" class="form-control btn btn-danger" value="X">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <button class="btn btn-fr text-white col-sm-12" id="adicionarCategoria" type="button">Adicionar Categoria</button>
                        </div>
                        <!--REGISTRO DE CATEGORIAS-->
                        <div class="row categorias">
                            @if(isset($Registro) && count($Categorias) > 0)
                                @foreach($Categorias as $c)
                                    @if(!empty($c))
                                        <div class="row ctg">
                                            <div class="col-sm-10">
                                                <label>Nome</label>
                                                <input type="text" name="Categoria[]" class="form-control" value="{{$c}}">
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Remover</label>
                                                <input type="button" id="removeCategoria" class="form-control btn btn-danger" value="X">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <button class="btn btn-fr text-white col-sm-12" id="adicionarModalidade" type="button">Adicionar Modalidade</button>
                        </div>
                        <!--REGISTRO DE MODALIDADES-->
                        <div class="row modalidades">
                            @if(isset($Registro) && count($Modalidades) > 0)
                                @foreach($Modalidades as $m)
                                    @if(!empty($m))
                                        <div class="row mdd">
                                            <div class="col-sm-10">
                                                <label>Nome</label>
                                                <input type="text" name="Modalidade[]" class="form-control" value="{{$m}}">
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Remover</label>
                                                <input type="button" id="removeModalidade" class="form-control btn btn-danger" value="X">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
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
            <!--//-->
        </div>
    </div>
</x-educacional-layout>