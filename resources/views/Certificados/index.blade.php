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
                        <option value="Inscritos" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Inscritos' ? 'selected' : ''}}>Inscritos</option>
                        <option value="Palestrantes" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Palestrantes' ? 'selected' : ''}}>Palestrantes</option>
                        <option value="Organizadores e Avaliadores" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Organizadores e Avaliadores' ? 'selected' : ''}}>Organizadores e Avaliadores</option>
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
                <input type="hidden" name="IDEvento" value="{{isset($_GET['evento']) ? $_GET['evento'] : ''}}">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Certificados/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Email</th>
                        <th style="text-align:center;" scope="col">Certificado</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                  <div class="row">
                    <div class="col-auto">
                        <button class="btn bg-fr text-white" type="submit">Emitir Certificados</button>
                    </div>
                  </div>
            </form>
            <!--//-->
        </div>
    </div>
    <script>
        $("select[name=evento]").on("change",function(){
            $("input[name=IDEvento]").val($(this).val())
        })

        $("select[name=Tipo]").on("change",function(){
            $("input[name=Tipo]").val($(this).val())
        })

        function setInscrito(IDInscrito){
            $("#inscrito_"+IDInscrito).val(IDInscrito)
        }
    </script>
</x-educacional-layout>