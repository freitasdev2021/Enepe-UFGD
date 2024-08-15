<x-educacional-layout>
    @if(in_array(Auth::user()->tipo,[1]))
    <div class="card">
        <div class="card-header bg-fr text-white">
            Disparar Notificações
        </div>
        <div class="card-body row">
            <form action="{{route('Notificacoes/Send')}}" method="POST">
                @csrf
                <div class="col-sm-12">
                    <label>Remetente</label>
                    <input type="email" class="form-control" name="Remetente" placeholder="Exemplo: remetente@ufgd.com.br" required>
                </div>
                <div class="col-sm-12">
                    <label>Título</label>
                    <input type="text" class="form-control" name="Titulo" placeholder="Exemplo: Aviso de Evento" required>
                </div>
                <div class="col-sm-12">
                    <label>Notificação</label>
                    <textarea class="form-control" name="Notificacao" placeholder="Exemplo: Novo evento a vista" required></textarea>
                </div>
                <br>
                <div class="col-sm-4">
                    <button class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header bg-fr text-white">
         Certificados
        </div>
        <div class="card-body row">
            @if(Count($Certificados))
                @foreach($Certificados as $c)
                <div class="card sala p-1" style="width: 18rem;">
                    <img src="{{asset('certificados/'.$c->Certificado)}}" class="card-img-top" alt="...">
                    <br>
                    <a href="{{asset('certificados/'.$c->Certificado)}}" download class="btn bg-fr text-white">Baixar</a>
                </div>
                @endforeach
            @else
            <h2 align="center">Você não tem Certificados</h2>
            @endif
        </div>
     </div>
     @elseif(in_array(Auth::user()->tipo,[2]))
     @if(count($Formularios) > 0)
     <div class="card">
        <div class="card-header bg-fr text-white">
            Formulários De Avaliação Disponíveis
        </div>
        <div class="card-body row">
            <hr>
            <table class="table table-sm tabela" id="escolas">
                <thead>
                    <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Formularios as $f)
                    <tr>
                        <td>{{$f->Titulo}}</td>
                        <td><a href="{{route('Formularios/Visualizar',$f->id)}}">Abrir</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
     </div>
     @endif
     <br>
     @elseif(in_array(Auth::user()->tipo,[3]))
     @if(count($Formularios) > 0)
     <div class="card">
        <div class="card-header bg-fr text-white">
            Formulários De Avaliação Disponíveis
        </div>
        <div class="card-body row">
            <hr>
            <table class="table table-sm tabela" id="escolas">
                <thead>
                    <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Formularios as $f)
                    <tr>
                        <td>{{$f->Titulo}}</td>
                        <td><a href="{{route('Formularios/Visualizar',$f->id)}}">Abrir</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
     </div>
     @endif
     <br>
     <div class="card">
        <div class="card-header bg-fr text-white">
         Certificados
        </div>
        <div class="card-body">
            @if(Count($Certificados))
                @foreach($Certificados as $c)
                <div class="card sala p-1" style="width: 18rem;">
                    <img src="{{asset('certificados/'.$c->Certificado)}}" class="card-img-top" alt="...">
                    <br>
                    <a href="{{asset('certificados/'.$c->Certificado)}}" download class="btn bg-fr text-white">Baixar</a>
                </div>
                @endforeach
            @else
            <h2 align="center">Você não tem Certificados</h2>
            @endif
        </div>
     </div>
     @endif
  </x-educacional-layout>