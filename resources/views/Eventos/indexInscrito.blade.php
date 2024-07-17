<x-educacional-layout>
    <div class="row salas">
        @foreach($Eventos as $e)
        <div class="card sala" style="width: 18rem;">
            <img src="{{asset('img/event.jpg')}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$e->Evento}}</h5>
              <p class="card-text">{{$e->Descricao}}</p>
              <a href="{{route('Eventos/Inscricao',$e->IDEvento)}}" class="btn bg-fr text-white">{{($e->Inscrito) ? 'Desinscrever' : 'Inscreva-se'}}</a>
            </div>
        </div>
        @endforeach
    </div>
</x-educacional-layout>