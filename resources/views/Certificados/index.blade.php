<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
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
            <!--CABECALHO-->
            <form class="col-sm-12 p-2 row" action="{{$_SERVER['PHP_SELF']}}" method="GET">
                <div class="col-auto">
                    <select name="Tipo" class="form-control" required>
                        <option value="">Selecione o Tipo de Participante</option>
                        <option value="Organizadores" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Organizadores' ? 'selected' : ''}}>Organizadores</option>
                        <option value="Apresentadores" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Apresentadores' ? 'selected' : ''}}>Apresentadores</option>
                        <option value="Telespectadores" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Telespectadores' ? 'selected' : ''}}>Telespectadores</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="evento" class="form-control" required>
                        <option value="">Selecione o Evento</option>
                        @foreach($Eventos as $e)
                        <option value="{{$e->id}}" {{isset($_GET['evento']) && $_GET['evento'] == $e->id ? 'selected' : ''}}>{{$e->Titulo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn bg-fr text-white" type="submit">Filtrar</button>
                </div>
            </form>
            <!--LISTAS-->
            <form class="col-sm-12 p-2" method="POST" action="{{route('Certificados/Save')}}">
                @csrf
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Certificados/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Email</th>
                        <th style="text-align:center;" scope="col">Certificado</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                  <div class="row">
                    <div class="col-auto">
                        <select class="form-control" name="Modelo">
                            <option value="">Selecione o Modelo</option>
                            @foreach($Modelos as $m)
                            <option value="{{$m->Arquivo}}">{{$m->Nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn bg-fr text-white" type="submit">Emitir Certificados</button>
                    </div>
                  </div>
            </form>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>