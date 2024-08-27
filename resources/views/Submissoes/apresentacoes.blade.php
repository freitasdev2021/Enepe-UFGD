<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <form class="fr-card-body" method="POST" action="{{route('Submissoes/Apresentacoes/Save')}}">
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
            <table class="table table-sm tabela" id="escolas" data-rota="{{route('Submissoes/Apresentacoes/list')}}">
                <thead>
                    <tr>
                        <th style="text-align:center;" scope="col">Titulo</th>
                        <th style="text-align:center;" scope="col">Autores</th>
                        <th style="text-align:center;" scope="col">Descrição</th>
                        <th style="text-align:center;" scope="col">Apresentadores</th>
                        <th style="text-align:center;" scope="col">Apresentou?</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            <button type="submit" class="btn btn-fr text-white">Enviar</button>
        </form>
    </div>
</x-educacional-layout>