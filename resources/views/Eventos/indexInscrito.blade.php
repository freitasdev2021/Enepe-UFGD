<x-educacional-layout>
    <div class="row salas">
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
        @foreach($Eventos as $e)
        <form action="{{route('Eventos/Entrar')}}" method="POST" class="card sala" style="width: 18rem;">
            @csrf
            <img src="{{url('storage/Site/'.$e->Capa)}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$e->Evento}}</h5>
              <p class="card-text">{{$e->Descricao}}</p>
              @if(!$e->Inscrito)
                @if($e->Inscricao != 'O prazo para a inscrição do evento está encerrado')
                    <button class="btn bg-fr text-white disabled">Inscrições Encerradas</button>
                @else
                    <a href="{{route('Eventos/Inscricao',$e->IDEvento)}}" class="btn bg-fr text-white">Inscreva-se</a>
                @endif
              @else
              <input type="hidden" name="IDEvento" value="{{$e->IDEvento}}">
              <button class="btn bg-fr text-white" type="submit">Entrar</button>
              @endif
            </div>
        </form>
        @endforeach
    </div>
</x-educacional-layout>