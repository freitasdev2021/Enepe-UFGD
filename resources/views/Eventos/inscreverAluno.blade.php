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
                    @if(isset($Registro))
                    <input type="hidden" name="id" value="{{$Registro->IDUser}}">
                    @endif
                    <input type="hidden" name="IDEvento" value="{{$IDEvento}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nome</label>
                            <input type="name" name="name" class="form-control" value="{{isset($Registro) ? $Registro->Nome : ''}}">
                        </div>
                        <div class="col-sm-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{isset($Registro) ? $Registro->Email : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Categoria</label>
                            <select name="Categoria" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Categorias as $c)
                                    @if(!empty($c))
                                        <option value="{{$c}}" {{isset($Registro) && $Registro->Categoria == $c ? 'selected' : ''}}>{{$c}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    @if(isset($Registro))
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Alterar Senha (A Nova Senha será enviada via Email)</label>
                            <input type="checkbox" class="form-check" name="alteraSenha">
                        </div>
                    </div>
                    @endif
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn col-auto bg-fr text-white">{{isset($Registro) ? 'Alterar Inscrição' : 'Efetivar Inscrição'}}</button>
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