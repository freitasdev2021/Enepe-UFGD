<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDEvento)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{$CurrentRoute}}" method="GET" class="row">
                    <div class="col-sm-10">
                        <label>Modalidades</label>
                        <select name="Modalidade" class="form-control">
                            @foreach($Modalidades as $m)
                                @if(!empty($m))
                                    <option value="{{$m}}" {{isset($_GET['Modalidade']) && $_GET['Modalidade'] == $m ? 'selected' : ''}}>{{$m}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label style="visibility:hidden">s</label>
                        <input type="submit" value="Filtrar" class="form-control">
                    </div>
                </form>
                <br>
                <form action="{{route('Eventos/Atividades/Save')}}" method="POST" class="row">
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
                    <div>
                        <input type="checkbox" name="mudarApresentacoes"> Mudar Apresentações?
                    </div>
                    @endif
                    <input type="hidden" name="IDEvento" value="{{$IDEvento}}">
                    <div class="row">
                        <div class="col-sm-8">
                            <label>Titulo</label>
                            <input type="text" name="Titulo" class="form-control" value="{{isset($Registro) ? $Registro->Titulo : ''}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Inicio</label>
                            <input type="datetime-local" name="Inicio" class="form-control" value="{{isset($Registro) ? $Registro->Inicio : ''}}" required>
                        </div>
                    </div>
                    <br>
                    <table class="table table-sm tabela" id="escolas">
                        <thead>
                            <tr>
                                <th style="text-align:center;" scope="col">Titulo</th>
                                <th style="text-align:center;" scope="col">Autores</th>
                                <th style="text-align:center;" scope="col">Descrição</th>
                                <th style="text-align:center;" scope="col">Apresentadores</th>
                                <th style="text-align:center;" scope="col">Vai Apresentar?</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Aprovados as $a)
                            <tr>
                                <td>{{$a->Titulo}}</td>
                                <td>{{$a->Autores}}</td>
                                <td>{{$a->Descricao}}</td>
                                <td>{{$a->Apresentador}}</td>
                                <td>
                                    <input type="checkbox" name="Apresentar[]" {{isset($Registro) && in_array($a->IDEntrega,$Apresentadores) ? 'checked' : ''}} value="{{$a->IDEntrega}}"> Sim
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Eventos/Atividades/index',$IDEvento)}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>