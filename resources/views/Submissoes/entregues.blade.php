<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDSubmissao)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
          <form action="{{$_SERVER['PHP_SELF']}}" method="GET">
            <div class="row">
              <label>Filtre por</label>
              <div class="col-sm-6">
                <select name="Modalidade" class="form-control">
                  <option value="">Selecione a Modalidade</option>
                  <option value="Ensino" {{(isset($_GET['Modalidade']) && $_GET['Modalidade'] == "Ensino") ? 'selected' : ''}}>Ensino</option>
                  <option value="Pesquisa" {{(isset($_GET['Modalidade']) && $_GET['Modalidade'] == "Pesquisa") ? 'selected' : ''}}>Pesquisa</option>
                  <option value="Extensão" {{(isset($_GET['Modalidade']) && $_GET['Modalidade'] == "Extensão") ? 'selected' : ''}}>Extensão</option>
                  <option value="Pós-Graduação" {{(isset($_GET['Modalidade']) && $_GET['Modalidade'] == "Pós-Graduação") ? 'selected' : ''}}>Pós-Graduação</option>
                </select>
              </div>
              <div class="col-sm-2">
                <input type="submit" class="btn btn-success" value="Filtrar">
              </div>
            </div>            
          </form>
          <hr>
            <!--LISTAS-->
            <form class="col-sm-12 p-2" action="{{route('Submissoes/Entregues/setAvaliador')}}" method="POST">
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
                <br>
                <input type="hidden" name="IDSubmissao" value="{{$IDSubmissao}}">
                <table class="table table-sm tabela" id="escolas" data-rota="{{ route('Submissoes/Entregues/list', $IDSubmissao) . (isset($_GET['Modalidade']) ? '?Modalidade=' . $_GET['Modalidade'] : '') }}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Título</th>
                        <th style="text-align:center;" scope="col">Categoria</th>
                        <th style="text-align:center;" scope="col">Inscrito</th>
                        <th style="text-align:center;" scope="col">Apresentador</th>
                        <th style="text-align:center;" scope="col">Avaliador</th>
                        <th style="text-align:center;" scope="col">Situação</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                  <br>
                  <div class="row">
                    <button class="btn bg-fr text-white col-auto">Salvar</button>
                  </div>
            </form>
            <!--//-->
        </div>
    </div>
    <script>
      function removerAtribuicao(IDEntrega){
        if(confirm("Deseja Remover a Atribuição de Avaliador desse Avaliador para essa Entrega?")){
          $.ajax({
              method : "GET",
              url : IDEntrega,
          }).done(function(response){
              window.location.reload()
          })
        }
      }
    </script>
</x-educacional-layout>