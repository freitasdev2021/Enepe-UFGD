<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$Trabalho->IDSubmissao)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <style>
            .resumo{
                display:flex;
                flex-wrap:wrap;
                width:100%;
                word-break: break-all;
            }
        </style>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Submissoes/Corrigir')}}" method="POST">
                    @csrf
                    @method("POST")
                    <input type="hidden" name="IDEntrega" value="{{$Trabalho->id}}">
                    <input type="hidden" name="IDSubmissao" value="{{$Trabalho->IDSubmissao}}">
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
                    <div class="row">
                        <div class="col-sm-12">
                            <h1>Título: {{$Trabalho->Titulo}}</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <h5>palavras-chave: {{$Trabalho->palavrasChave}}</h5>
                        </div>
                    </div>
                    <hr>
                    <div class="row resumo">
                        {{$Trabalho->Descricao}}
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Situação</label>
                            <select name="Status" class="form-control">
                                <option value="">Selecione</option>
                                <option value="Aprovado">Aprovado</option>
                                <option value="Aprovado com Ressalvas">Aprovado com Ressalvas</option>
                                <option value="Reprovado">Reprovado</option>
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label>Feedback</label>
                            <textarea class="form-control" maxlength="100" name="Feedback" required></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Corrigir</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{(Auth::user()->tipo == 3) ? route('Submissoes/index') : route('Submissoes/Entregues',$Trabalho->IDSubmissao)}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>