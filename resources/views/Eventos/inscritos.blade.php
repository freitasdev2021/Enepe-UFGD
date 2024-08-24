<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDEvento)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Eventos/Inscricoes/inscreverAluno',$IDEvento)}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Eventos/Inscricoes/list',$IDEvento)}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Categoria</th>
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
        function apagarInscrito(link,Certificou,Entregou){
            if(Certificou == 1 || Entregou == 1){
                alert("Não há Possibilidade de Excluir a Inscrição, pois o Inscrito ja Submeteu Trabalho(s) e(ou) Tem Certificado(s)")
            }else{
                if(confirm("Deseja Excluir a Inscrição?")){
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