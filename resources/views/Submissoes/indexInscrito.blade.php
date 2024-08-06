<x-educacional-layout>
    <div class="row salas">
        @foreach($Submissoes as $s)
        <div class="card sala" style="width: 18rem;">
            <img src="{{asset('img/submissao.jpg')}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$s->Categoria}}</h5>
              <p class="card-text">
                <a href="{{url('storage/regras_submissao/'.$s->Regras)}}" download><i class='bx bxs-download'></i>Regras</a>
              </p>
              <a href="{{route('Submissoes/Entrega',$s->id)}}" class="btn bg-fr text-white">Visualizar</a>
            </div>
        </div>
        @endforeach
    </div>
</x-educacional-layout>