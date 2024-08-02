<x-educacional-layout>
    <form action="" method="POST">
        @csrf
        <div class="shadow p-2">
            <h4 align="center" class="bg-fr text-white p-2">Personalize o Site, as Inscrições são feitas na plataforma com usuários cadastrados, e as palestras e demais dados são Sincronizadas Automaticamente</h4>
            <br>
            <div class="row">
                <div class="col-sm-6">
                    <label>Evento</label>
                    <select class="form-control" name="Evento">
                        <option value="">Selecione</option>

                    </select>
                </div>
                <div class="col-sm-6">
                    <label>Contatos, Separe-os por Vírgula</label>
                    <input type="text" name="Contatos" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <label>Capa do Evento</label>
                    <img src="" id="capa" height="500px" width="100%">
                    <input type="file" name="Capa" style="display:none;" onchange="displaySelectedImage(event, 'capa')">
                </div>
            </div>
            <hr>
            <button class="btn bg-fr text-white">Salvar</button>
        </div>
    </form>
</x-educacional-layout>
<script>
    $("#capa").on("click",function(){
        $("input[name=Capa]").trigger("click")
    })
    //
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