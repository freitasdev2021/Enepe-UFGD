<x-educacional-layout>
    <div class="card">
       <div class="card-header bg-fr text-white">
        Formulário de Inscrição - {{$Evento->Titulo}}
       </div>
       <div class="card-body">
          <form action="{{route('Eventos/Inscrever')}}" method="POST">
            @csrf
            <input type="hidden" name="IDEvento" value="{{$Evento->id}}">
            <div class="col-sm-12">
                <label>Categoria</label>
                <select name="Categoria" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Ensino">Ensino</option>
                    <option value="Pesquisa">Pesquisa</option>
                    <option value="Extensão">Extensão</option>
                    <option value="Pós-Graduação">Pós-Graduação</option>
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