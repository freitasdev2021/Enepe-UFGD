<x-educacional-layout>
    @if(in_array(Auth::user()->tipo,[0,1]))
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
     @elseif(in_array(Auth::user()->tipo,[0,2]))
     
     @elseif(in_array(Auth::user()->tipo,[0,3]))
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