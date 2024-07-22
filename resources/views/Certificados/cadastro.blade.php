<x-educacional-layout>
    <div class="card">
       <div class="card-header bg-fr text-white">
        Enviar Modelo JPG/JPEG
       </div>
       <div class="card-body">
          <form action="{{route('Certificados/Modelos/Save')}}" method="POST" enctype="multipart/form-data">
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
                <label>Nome do Modelo</label>
                <input type="name" class="form-control" name="Nome">
            </div>
            <div class="col-sm-12">
                <label>Modelo</label>
                <input type="file" class="form-control" onchange="displaySelectedImage(event, 'selectedModelo')" name="Arquivo" accept="image/*">
            </div>
            <div class="col-sm-12">
                <br>
                <img src="" width="100%" height="500px" id="selectedModelo">
            </div>
            <br>
            <div class="col-sm-12">
                <button class="btn col-auto bg-fr text-white">Salvar modelo</button>
                &nbsp;
                <a href="{{route('Certificados/Modelos')}}" class="btn btn-default">Voltar</a>
            </div>
          </form>
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