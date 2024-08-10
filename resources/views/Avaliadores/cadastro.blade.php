<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Avaliadores/Save')}}" method="POST">
                    @csrf
                    @method("POST")
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
                    @if(isset($Registro))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Nome</label>
                            <input type="text" name="name" class="form-control" value="{{isset($Registro) ? $Registro->name : ''}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" value="{{(isset($Registro)) ? $Registro->email : ''}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Evento</label>
                            <select name="IDEvento" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Eventos as $e)
                                <option value="{{$e->id}}">{{$e->Titulo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if(isset($Registro))
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Senha</label>
                            <br>
                            <input type="checkbox" name="alteraSenha">&nbsp;Alterar Senha
                        </div>
                    </div>
                    @endif
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Avaliadores/index')}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>