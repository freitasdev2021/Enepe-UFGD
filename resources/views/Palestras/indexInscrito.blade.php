<x-educacional-layout>
    <div class="row salas">
        @foreach($Palestras as $p)
        <div class="card sala" style="width: 18rem;">
            <img src="{{url('storage/palestrantes/'.$p->Foto)}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$p->Titulo}}</h5>
              <br>
              <h6>{{$p->Nome}}</h6>
              <p class="card-text">{{$p->Palestra}}</p>
            </div>
        </div>
        @endforeach
    </div>
</x-educacional-layout>