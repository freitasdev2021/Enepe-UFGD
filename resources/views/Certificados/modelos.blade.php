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
              <button data-excluir="{{route('Modelos/Excluir',$m->id)}}" type="button" class="btn delete btn-danger">Excluir</button>
            </div>
        </div>
        @endforeach
    </div>
    <script>
        $(".delete").on("click",function(){
            if(confirm("Deseja Excluir esse Modelo?")){
                $.ajax({
                    url : $(this).attr("data-excluir")
                }).done(function(response){
                    console.log(response)
                    window.location.reload()
                })
            }
        })
    </script>
</x-educacional-layout>