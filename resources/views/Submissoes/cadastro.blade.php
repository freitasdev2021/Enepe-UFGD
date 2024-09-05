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
                <form action="{{route('Submissoes/Save')}}" method="POST" enctype="multipart/form-data">
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
                            <label>Min. Palavras</label>
                            <input type="number" name="MinLength" class="form-control" value="{{isset($Registro) ? $Registro->MinLength : ''}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Max. Palavras</label>
                            <input type="number" name="MaxLength" class="form-control" value="{{isset($Registro) ? $Registro->MaxLength : ''}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Modalidade</label>
                            <select name="Categoria" class="form-control">
                                @foreach($Modalidades as $m)
                                    @if(!empty($m))
                                        <option value="{{$m}}" {{isset($Registro) && $Registro->Categoria == $m ? 'selected' : ''}}>{{$m}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Regras</label>
                            <input type="file" class="form-control" name="Regras">
                            <input type="hidden" name="oldRegras" value="{{isset($Registro) && $Registro->Regras}}" accept="application/pdf">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Submissoes/index')}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>