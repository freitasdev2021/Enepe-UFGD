<x-educacional-layout>
    @if(Auth::user()->tipo == 3)
        <form class="row" action="{{route('Atividades/index')}}" method="GET">
            <div class="col-sm-10">
                <select class="form-control" name="Apresentacoes">
                    <option value="Todas" {{isset($_GET['Apresentacoes']) && $_GET['Apresentacoes'] == "Todas" ? 'selected' : ''}}>Todas as Apresentações</option>
                    <option value="Minhas" {{isset($_GET['Apresentacoes']) && $_GET['Apresentacoes'] == "Minhas" ? 'selected' : ''}}>Minhas Apresentações</option>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="submit" value="Filtrar" class="form-control">
            </div>
        </form>
    @endif
    <div class="row salas">
        @foreach($Atividades as $a)
        <div class="card sala" style="width: 18rem;">
            <img src="{{asset('img/presentation.jpg')}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$a->Titulo}}</h5>
              <br>
              <p class="card-text">{{date('d/m/Y H:i:s',strtotime($a->Inicio))}}</p>
              <p class="card-text">{{$a->Descricao}}</p>
              <ul>
                @foreach(json_decode($a->listaApresentacoes) as $ap)
                <li>{{$ap->titulo}} ({{$ap->apresentador}})</li>
                @endforeach
              </ul>
              <a href="{{route('Atividades/Atividade',$a->id)}}" class="btn bg-fr text-white">Entrar</a>
            </div>
        </div>
        @endforeach
    </div>
</x-educacional-layout>