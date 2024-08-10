<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDEvento)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Eventos/Atividades/Save')}}" method="POST">
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
                    <input type="hidden" name="IDEvento" value="{{$IDEvento}}">
                    <div class="row">
                        <div class="col-sm-8">
                            <label>Titulo</label>
                            <input type="text" name="Titulo" class="form-control" value="{{isset($Registro) ? $Registro->Titulo : ''}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Inicio</label>
                            <input type="datetime-local" name="Inicio" class="form-control" value="{{isset($Registro) ? $Registro->Inicio : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição</label>
                            <textarea name="Descricao" class="form-control">{{isset($Registro) ? $Registro->Descricao : ''}}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Eventos/Atividades/index',$IDEvento)}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>