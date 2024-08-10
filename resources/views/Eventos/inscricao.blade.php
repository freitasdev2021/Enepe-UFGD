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
                    <option value="Ensino">Ensino</option>
                    <option value="Pesquisa">Pesquisa</option>
                    <option value="Extensão">Extensão</option>
                    <option value="Pós-Graduação">Pós-Graduação</option>
                    <option value="Aluno de Iniciação Científica da UFGD">Aluno de Iniciação Científica da UFGD</option>
                    <option value="Aluno da Graduação da UFGD">Aluno da Graduação da UFGD</option>
                    <option value="Aluno da Pós-Graduação da UFGD">Aluno da Pós-Graduação da UFGD</option>
                    <option value="Docente da UFGD">Docente da UFGD</option>
                    <option value="Técnico Administrativo da UFGD">Técnico Administrativo da UFGD</option>
                    <option value="Outro">Outro</option>
                    <option value="Avaliador(a) externo(a)">Avaliador(a) externo(a)</option>
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