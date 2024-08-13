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
                <form action="{{route('Palestras/Save')}}" method="POST">
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
                        <div class="col-sm-4">
                            <label>Título</label>
                            <input type="text" name="Titulo" class="form-control" value="{{isset($Registro) ? $Registro->Titulo : ''}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Evento</label>
                            <select name="IDEvento" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($eventos as $e)
                                <option value="{{$e->id}}" {{isset($Registro) && $Registro->IDEvento == $e->id ? 'selected' : ''}}>{{$e->Titulo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Palestrante</label>
                            <select name="IDPalestrante" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($palestrantes as $p)
                                <option value="{{$p->id}}" {{isset($Registro) && $Registro->IDPalestrante == $p->id ? 'selected' : ''}}>{{$p->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Data</label>
                            <input type="date" name="Data" class="form-control" value="{{(isset($Registro)) ? $Registro->Data : ''}}" >
                        </div>
                        <div class="col-sm-4">
                            <label>Início</label>
                            <input type="time" name="Inicio" class="form-control" value="{{(isset($Registro)) ? $Registro->Inicio : ''}}" >
                        </div>
                        <div class="col-sm-4">
                            <label>Término</label>
                            <input type="time" name="Termino" class="form-control" value="{{(isset($Registro)) ? $Registro->Termino : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição</label>
                            <textarea name="Palestra" class="form-control">{{isset($Registro) ? $Registro->Palestra : ''}}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Palestras/index')}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>