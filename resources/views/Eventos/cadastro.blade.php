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
                            <input type="file" name="Capa" style="display:none;" onchange="displaySelectedImage(event, 'capa')">
                            <input type="hidden" name="oldCapa" value="{{isset($Registro) ? $Registro->Capa : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Título</label>
                            <input type="text" name="Titulo" class="form-control" value="{{isset($Registro) ? $Registro->Titulo : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início</label>
                            <input type="datetime-local" name="Inicio" class="form-control" value="{{(isset($Registro)) ? $Registro->Inicio : ''}}" >
                        </div>
                        <div class="col-sm-6">
                            <label>Término</label>
                            <input type="datetime-local" name="Termino" class="form-control" value="{{(isset($Registro)) ? $Registro->Termino : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início das Inscrições</label>
                            <input type="date" name="INIInscricao" class="form-control" value="{{(isset($Registro)) ? $Registro->INIInscricao : ''}}" >
                        </div>
                        <div class="col-sm-6">
                            <label>Término das Inscrições</label>
                            <input type="date" name="TERInscricoes" class="form-control" value="{{(isset($Registro)) ? $Registro->TERInscricoes : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Início das Submissões</label>
                            <input type="datetime-local" name="INISubmissao" class="form-control" value="{{(isset($Registro)) ? $Registro->INISubmissao : ''}}" >
                        </div>
                        <div class="col-sm-6">
                            <label>Término das Submissões</label>
                            <input type="datetime-local" name="TERSubmissao" class="form-control" value="{{(isset($Registro)) ? $Registro->TERSubmissao : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Normas de Submissão</label>
                            <textarea name="Normas" class="form-control">{{isset($Registro) ? $Registro->Normas : ''}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição</label>
                            <textarea name="Descricao" class="form-control">{{isset($Registro) ? $Registro->Descricao : ''}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <button class="btn btn-fr text-white col-sm-12" id="adicionar" type="button">Adicionar Contato</button>
                        </div>
                    </div>
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
                    <div class="row contatos">
                        @if(isset($Registro))
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
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Eventos/index')}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>
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
    //
    $('#adicionar').click(function() {
        $('.contatos').append($(".contato").html());
    });
    //
    $(".contatos").on("click","#removeContato",function(){
        $(this).parents(".ctt").remove()
    })
    //
</script>