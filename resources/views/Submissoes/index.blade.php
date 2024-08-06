<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            @if(Auth::user()->tipo == 1)
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Submissoes/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Submissoes/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Evento</th>
                        <th style="text-align:center;" scope="col">Modalidade</th>
                        <th style="text-align:center;" scope="col">Regras</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
            </div>
            <!--//-->
            @elseif(Auth::user()->tipo == 2)
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Submissoes/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Evento</th>
                        <th style="text-align:center;" scope="col">Inscrito</th>
                        <th style="text-align:center;" scope="col">Regras</th>
                        <th style="text-align:center;" scope="col">Categoria</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
            </div>
            <!--//-->
            @endif
        </div>
    </div>
</x-educacional-layout>