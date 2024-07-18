<x-educacional-layout>
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.9.7/css/bootstrap.css" />
      <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.9.7/css/react-select.css" />
    <div class="row salas">
        @foreach($Atividades as $a)
        <div class="card sala" style="width: 18rem;">
            <img src="{{asset('img/presentation.jpg')}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$a->Titulo}}</h5>
              <br>
              <p class="card-text">{{$a->Inicio}}</p>
              <h6>{{$a->Sala}}</h6>
              <p class="card-text">{{$a->Descricao}}</p>
              <a href="{{route('Atividades/Atividade',$a->id)}}" class="btn bg-fr text-white">Entrar</a>
            </div>
        </div>
        @endforeach
    </div>
</x-educacional-layout>