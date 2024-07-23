<x-educacional-layout>
    <div class="row">
        <div class="col-auto">
            <a href="{{route("Certificados/Modelos/Novo")}}" class="btn bg-fr text-white">Adicionar</a>
        </div>
        <div class="col-auto">
            <a href="{{route('Certifica/index')}}" class="btn btn-light">Voltar</a>
        </div>
    </div>
    <div class="row salas">
        @foreach($Modelos as $m)
        <div class="card sala" style="width: 18rem;">
            <img src="{{url('storage/modelos/'.$m->Arquivo)}}" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">{{$m->Nome}}</h5>
              <br>
              <a href="{{route('Atividades/Atividade',$m->id)}}" class="btn btn-danger">Excluir</a>
            </div>
        </div>
        @endforeach
    </div>
</x-educacional-layout>