<x-educacional-layout>
    <div class="row">
        <div class="col-sm-6">
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
                <div class="row">
                    <div class="col-sm-12">
                        <label>Título</label>
                        @if(session('Submissao'))
                            <input type="text" name="Titulo" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Titulo : $Entrega['Titulo'] }}" required>
                        @else
                            <input type="text" name="Titulo" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Titulo : '' }}" required>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Autores (Separe por ',')</label>
                        @if(session('Submissao'))
                        <input type="text" name="Autores" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Autores : $Entrega['Autores'] }}">
                        @else
                        <input type="text" name="Autores" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Autores : '' }}">
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label>palavras-chaves (Separe por ',')</label>
                        @if(session('Submissao'))
                        <input type="text" name="palavrasChave" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->palavrasChave : $Entrega['palavrasChave'] }}">
                        @else
                        <input type="text" name="palavrasChave" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->palavrasChave : '' }}">
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Apresentador</label>
                        @if(session('Submissao'))
                        <input name="Apresentador" type="text" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Apresentador : $Entrega['Apresentador'] }}">
                        @else
                        <input name="Apresentador" type="text" class="form-control" value="{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Apresentador : '' }}">
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label>Área Temática</label>
                        <select name="Tematica" class="form-control" required>
                            <option>Selecione</option>
                            @if(session('Submissao'))
                                @foreach($Tematica as $t)
                                <option value="{{$t}}" {{in_array(Auth::user()->tipo,[1,2]) && $Entregas[0]->Tematica == $Entrega['Tematica'] ? 'selected' : '' }}>{{$t}}</option>
                                @endforeach
                            @else
                                @foreach($Tematica as $t)
                                <option value="{{$t}}" {{in_array(Auth::user()->tipo,[1,2]) && $Entregas[0]->Tematica == $t ? 'selected' : '' }}>{{$t}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Resumo</label>
                        @if(session('Submissao'))
                        <textarea name="Descricao" class="form-control" required >{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Descricao : $Entrega['Descricao'] }}</textarea>
                        @else
                        <textarea name="Descricao" class="form-control" required >{{in_array(Auth::user()->tipo,[1,2]) ? $Entregas[0]->Descricao : '' }}</textarea>
                        @endif
                    </div>
                </div>
                <br>
                <div class="col-sm-12 text-left row">
                    @if($Evento->TERSubmissao > NOW() && isset($Entregas[0]) && in_array($Entregas[0]->Situacao,['Aprovado com Ressalvas']) || count($Entregas) == 0)
                    <button class="btn bg-fr text-white col-auto">Salvar</button>
                    @endif
                    &nbsp;
                    <a class="btn btn-light col-auto" href="{{(Auth::user()->tipo == 3) ? route('Submissoes/index') : route('Submissoes/Entregues',$IDSubmissao)}}">Voltar</a>
                </div>
            </form>
        </div>
        <div class="col-sm-6">
            <label style="visibility: hidden">a</label>
            <table class="table">
                <thead class="bg-fr text-white">
                    <tr>
                        <th colspan="5" style="text-align:center;">Entregas</th>
                    </tr>
                    <tr>
                        <th>Titulo</th>
                        <th>Entrega</th>
                        <th>Situação</th>
                        <th>Feedback</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <body>
                    @foreach($Entregas as $e)
                    <tr>
                        <td>{{$e->Titulo}}</td>
                        <td>{{date('d/m/Y',strtotime($e->created_at))}}</td>
                        <td>{{$e->Situacao}}</td>
                        <td>{{$e->Feedback}}</td>
                        @if(in_array($e->Situacao,['Aprovado com Ressalvas']))
                        <td>
                            <button class="btn btn-light revisar" data-trabalho="{{route('Submissoes/getTrabalho',$e->id)}}">Revisar</button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </body>
            </table>
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