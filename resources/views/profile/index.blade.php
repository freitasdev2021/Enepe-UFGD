<x-educacional-layout>
    <div class="card">
       <div class="card-header bg-fr text-white">
        Atualização de Senha
       </div>
       <div class="card-body">
          <form action="{{route('Configuracoes/UpdatePassword')}}" method="POST">
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
            <div class="col-sm-12">
                <label>Nova senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <br>
            <div class="col-sm-12">
                <label>Confirme sua Nova senha</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <br>
            <div class="col-sm-12">
                <button class="btn col-auto bg-fr text-white">Resetar Senha</button>
            </div>
          </form>
       </div>
    </div>
 </x-educacional-layout>