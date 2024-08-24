<x-educacional-layout>
    <div class="card">
       <div class="card-header bg-fr text-white">
        Formulário de Inscrição - {{$Evento->Titulo}}
       </div>
       <div class="card-body">
          <form action="{{route('Eventos/Inscrever')}}" method="POST">
            @csrf
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
            <input type="hidden" name="IDEvento" value="{{$Evento->id}}">
            <div class="col-sm-12">
                <label>Categoria</label>
                <select name="Categoria" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($Categorias as $c)
                        @if(!empty($c))
                            <option value="{{$c}}">{{$c}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <br>
            <div class="col-sm-12">
                <button class="btn col-auto bg-fr text-white">Efetivar Inscrição</button>
                &nbsp;
                <a href="{{route('Eventos/index')}}" class="btn btn-default">Voltar</a>
            </div>
          </form>
       </div>
    </div>
 </x-educacional-layout>