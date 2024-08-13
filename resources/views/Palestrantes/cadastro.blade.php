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
                <form action="{{route('Palestrantes/Save')}}" method="POST" enctype="multipart/form-data">
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
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{ isset($Registro) ? url('storage/palestrantes/'.$Registro->Foto) : asset('img/palAvatar.png') }}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                <label class="form-label text-white m-1" for="customFile2">Upload Foto</label>
                                <input type="file" name="Foto" class="form-control d-none" id="customFile2" onchange="displaySelectedImage(event, 'selectedAvatar')" accept="image/jpg,image/png,image/jpeg" {{!isset($Registro) ? 'required' : ''}} />
                                <input name="oldFoto" type="hidden" value="{{isset($Registro) ? $Registro->Foto : ''}}">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nome</label>
                            <input type="text" name="Nome" class="form-control" value="{{isset($Registro) ? $Registro->Nome : ''}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Email</label>
                            <input type="email" name="Email" class="form-control" value="{{isset($Registro) ? $Registro->Email : ''}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Currículo</label>
                            <textarea name="Curriculo" class="form-control">{{isset($Registro) ? $Registro->Curriculo : ''}}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button class="btn bg-fr text-white col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Palestrantes/index')}}">Voltar</a>
                    </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        function displaySelectedImage(event, elementId) {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    </script>
</x-educacional-layout>