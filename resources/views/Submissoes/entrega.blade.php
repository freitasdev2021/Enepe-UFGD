<x-educacional-layout>
    <div class="row">
        <div class="col-sm-12">
            <form action="{{route('Submissoes/Entregas/Save')}}" method="POST">
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
                <label style="visibility: hidden">a</label>
                @if($Evento->TERSubmissao < NOW())
                <div class="row d-flex justify-content-center">
                    <div class="p-2 col-sm-10 bg-warning d-flex justify-content-center">
                        <strong>O prazo para a submissão de resumos está encerrado</strong>
                    </div>
                </div>
                @endif
                {{-- <pre>
                    {{dd($debug)}}
                </pre> --}}
                <br>
                <input type="hidden" name="IDSubmissao" value="{{$IDSubmissao}}">
                <input type="hidden" name="IDEntrega" value="{{in_array(Auth::user()->tipo,[1,2]) ? $IDEntrega: '' }}">
                <div class="p-2 bg-fr text-white rounded">
                    <h3>Regras</h3>
                    <ul>
                        <li>Quantidade Mínima de Palavras: {{$Submissao->MinLength}}</li>
                        <li>Quantidade Máxima de Palavras: {{$Submissao->MaxLength}}</li>
                    </ul>
                </div>
                <br>
                {{-- @if(session('Submissao'))
                {{dd(session('Submissao'))}}
                @endif --}}
                @if(!empty($Entregas))
                    <div class="bg-secondary p-2">
                        <strong>Título:</strong>
                        <p>
                            <strong>{{(Auth::user()->tipo == 1) ? $Entregas->Titulo : $Entregas[0]->Titulo}}</strong>
                        </p>
                    </div>
                    <br>
                    <div class="bg-warning p-2">
                        <strong>{{(Auth::user()->tipo == 1) ? $Entregas->Status : $Entregas[0]->Status}}</strong>
                    </div>
                    <br>
                    <div class="bg-primary p-2">
                        <strong>Feedback:</strong>
                        <br>
                        <p>
                            <strong>{{(Auth::user()->tipo == 1) ? $Entregas->Feedback : $Entregas[0]->Feedback}}</strong>
                        </p>
                    </div>
                @endif
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Título</label>
                        @if(session('Submissao'))
                            <input type="text" name="Titulo" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Titulo : $Entrega['Titulo'] }}" required>
                        @else
                            <input type="text" name="Titulo" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Titulo : '' }}" required>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Autores (Separe por ',')</label>
                        @if(session('Submissao'))
                        <input type="text" name="Autores" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Autores : $Entrega['Autores'] }}" required>
                        @else
                        <input type="text" name="Autores" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Autores : '' }}" required>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label>palavras-chaves (Separe por ',')</label>
                        @if(session('Submissao'))
                        <input type="text" name="palavrasChave" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->palavrasChave : $Entrega['palavrasChave'] }}" required>
                        @else
                        <input type="text" name="palavrasChave" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->palavrasChave : '' }}" required>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Apresentador</label>
                        @if(session('Submissao'))
                        <input name="Apresentador" type="text" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Apresentador : $Entrega['Apresentador'] }}" required>
                        @else
                        <input name="Apresentador" type="text" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Apresentador : '' }}" required>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label>Área Temática</label>
                        <select name="Tematica" class="form-control" required>
                            <option>Selecione</option>
                            @if(session('Submissao'))
                                @foreach($Tematica as $t)
                                <option value="{{$t}}" {{in_array(Auth::user()->tipo,[1,2]) && $Entregas->Tematica == $Entrega['Tematica'] ? 'selected' : '' }}>{{$t}}</option>
                                @endforeach
                            @else
                                @foreach($Tematica as $t)
                                <option value="{{$t}}" {{in_array(Auth::user()->tipo,[1,2]) && $Entregas->Tematica == $t ? 'selected' : '' }}>{{$t}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Resumo</label>
                        @if(session('Submissao'))
                        <textarea name="Descricao" class="form-control" required >{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Descricao : $Entrega['Descricao'] }}</textarea>
                        @else
                        <textarea name="Descricao" class="form-control" required >{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas->Descricao : '' }}</textarea>
                        @endif
                    </div>
                </div>
                <br>
                {{-- {{dd($debug)}} --}}
                <div class="col-sm-12 text-left row">
                    @if(empty($Entregas) || !empty($Entregas) && $Status == "Aprovado com Ressalvas" || $Status = "Aguardando Correção" && Auth::user()->tipo == 1)
                        <button class="btn bg-fr text-white col-auto">Salvar</button>&nbsp;
                        @if(!empty($Entregas) && Auth::user()->tipo == 3 && $Status == "Aprovado com Ressalvas")
                            <button class="btn btn-warning revisar col-auto" data-trabalho="{{route('Submissoes/getTrabalho',$IDEntrega)}}">Revisar</button>
                        @endif
                    @endif
                    &nbsp;
                    <a class="btn btn-light col-auto" href="{{(Auth::user()->tipo == 3) ? route('Submissoes/index') : route('Submissoes/Entregues',$IDSubmissao)}}">Voltar</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(".revisar").on("click",function(){
            $.ajax({
                method : "GET",
                url : $(this).attr("data-trabalho"),
            }).done(function(response){
                resp = jQuery.parseJSON(response)
                $("input[name=IDEntrega]").val(resp.id)
                $("input[name=Autores]").val(resp.Autores)
                $("input[name=Titulo]").val(resp.Titulo)
                $("input[name=palavrasChave]").val(resp.palavrasChave)
                $("textarea[name=Descricao]").val(resp.Descricao)
                $("input[name=IDSubmissao]").val(resp.IDSubmissao)
                $("input[name=Apresentador]").val(resp.Apresentador)
                $("select[name=Tematica]").val(resp.Tematica)
            })
        })
    </script>
</x-educacional-layout>