<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Avaliadores/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Avaliadores/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Email</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        function apagarAvaliador(link,Certificou,Avaliou){
            if(Certificou == 1 || Avaliou == 1){
                alert("Não há Possibilidade de Excluir o Avaliador, pois o Avaliador ja Corrigiu Trabalho(s) e(ou) Tem Certificado(s)")
            }else{
                if(confirm("Deseja Excluir o Avaliador?")){
                    $.ajax({
                        method : 'GET',
                        url : link
                    }).done(function(response){
                        window.location.reload()
                    })
                }
            }
        }
    </script>
</x-educacional-layout>