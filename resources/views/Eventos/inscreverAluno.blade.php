<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDEvento)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Eventos/saveInscricaoAluno')}}" method="POST">
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
                    <input type="hidden" name="IDEvento" value="{{$IDEvento}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nome</label>
                            <input type="name" name="name" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="row">
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
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn col-auto bg-fr text-white">Efetivar Inscrição</button>
                            &nbsp;
                            <a href="{{route('Eventos/Inscricoes',$IDEvento)}}" class="btn btn-default">Voltar</a>
                        </div>
                    </div>
                  </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>