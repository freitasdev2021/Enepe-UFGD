<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
                <div class="col-sm-12 p-2">
                    <div class="card-body" style="height: calc(100vh - 150px); overflow-y:scroll">
                        <dl>
                            <dt>Submissões</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Submissões</strong> > <strong>Abrir</strong>
                                    </li>
                                </ul>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>