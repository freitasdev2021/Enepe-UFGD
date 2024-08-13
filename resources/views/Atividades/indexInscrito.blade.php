<x-educacional-layout>
    <div class="row salas">
        @foreach($Atividades as $a)
        <div class="card sala" style="width: 18rem;">
            <img src="{{asset('img/presentation.jpg')}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$a->Titulo}}</h5>
              <br>
              <p class="card-text">{{date('d/m/Y H:i:s',strtotime($a->Inicio))}}</p>
              <p class="card-text">{{$a->Descricao}}</p>
              <a href="{{route('Atividades/Atividade',$a->id)}}" class="btn bg-fr text-white">Entrar</a>
            </div>
        </div>
        @endforeach
    </div>
</x-educacional-layout>