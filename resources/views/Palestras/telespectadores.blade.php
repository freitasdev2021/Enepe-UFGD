<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDPalestra)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form class="col-sm-12 p-2" method="POST" action="{{route('Palestras/Presenca')}}">
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
                <input type="hidden" value="{{$IDPalestra}}" name="IDPalestra">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Palestras/Participantes/list',$IDPalestra)}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Inscrito</th>
                        <th style="text-align:center;" scope="col">Assistiu?</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-auto">
                        <button type="submit" class="btn bg-fr text-white">Salvar</button>
                    </div>
                    <div class="col-auto">
                        <a href="{{route('Palestras/index')}}" class="btn btn-default col-auto">Voltar</a>
                    </div>
                </div>
            </form>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>