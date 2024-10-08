<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
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
            <!--CABECALHO-->
            <form class="col-sm-12 p-2 row" action="{{route(Route::currentRouteName())}}" method="GET">
                <div class="col-auto">
                    <select name="Tipo" class="form-control" required>
                        <option value="">Selecione o Tipo de Participante</option>
                        <option value="Inscritos" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Inscritos' ? 'selected' : ''}}>Inscritos</option>
                        <option value="Palestrantes" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Palestrantes' ? 'selected' : ''}}>Palestrantes</option>
                        <option value="Organizadores e Avaliadores" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Organizadores e Avaliadores' ? 'selected' : ''}}>Organizadores e Avaliadores</option>
                        <option value="Fizeram a Avaliação" {{isset($_GET['Tipo']) && $_GET['Tipo'] == 'Fizeram a Avaliação' ? 'selected' : ''}}>Fizeram a Avaliação</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn bg-fr text-white" type="submit">Filtrar</button>&nbsp;
                    <a href='{{route('Certificados/Disponibilizar')}}' class="btn btn-success">Disponibilizar para Todos do Evento</a>
                </div>
            </form>
            <!--LISTAS-->
            <form class="col-sm-12 p-2" method="POST" action="{{route('Certificados/Save')}}">
                @csrf
                <input type="hidden" name="IDEvento" value="{{isset($_GET['evento']) ? $_GET['evento'] : ''}}">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Certificados/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Disponibilidade</th>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Email</th>
                        <th style="text-align:center;" scope="col">Certificado</th>
                        <th style="text-align:center;" scope="col">Ver</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                  <div class="row">
                    <div class="col-auto">
                        <button class="btn bg-fr text-white" type="submit">Emitir Certificados</button>
                    </div>
                  </div>
            </form>
            <!--//-->
        </div>
    </div>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> --}}
    <script>
        $("select[name=evento]").on("change",function(){
            $("input[name=IDEvento]").val($(this).val())
        })

        $("select[name=Tipo]").on("change",function(){
            $("input[name=Tipo]").val($(this).val())
        })

        function delCertificado(url){
            $.ajax({
                url: url,
                method : 'GET'
            }).done(function(response){
                window.location.reload()
            })
        }

        function setInscrito(IDInscrito){
            $("#inscrito_"+IDInscrito).val(IDInscrito)
        }

        function convertJpgToPdf(caminho){
            alert("teste")
            return false
            const { jsPDF } = window.jspdf;
            // Definir o PDF com tamanho personalizado para 1920x1080 pixels em modo paisagem
            const pdf = new jsPDF({
                orientation: 'landscape', // Orientação paisagem
                unit: 'px', // Unidades em pixels
                format: [1920, 1080], // Tamanho da página em pixels
            });
            
            const img = new Image();
            img.src = caminho; // Substitua pelo caminho da sua imagem

            img.onload = function() {
                // Adicionar a imagem ao PDF e ajustá-la para ocupar toda a página
                pdf.addImage(img, 'JPEG', 0, 0, 1920, 1080);
                
                // Salvar o PDF
                pdf.save('certificado.pdf');
            };
        }
    </script>
</x-educacional-layout>