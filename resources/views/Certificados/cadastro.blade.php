<x-educacional-layout>
    <div class="row">
        <div class="col-sm-9">
            <div class="card">
                <div class="card-header bg-fr text-white">
                 Enviar Modelo JPG/JPEG
                </div>
                <div class="card-body" style="height: calc(100vh - 150px); overflow-y:scroll">
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
                     @if(isset($Registro))
                     <input type="hidden" name="id" value="{{$Registro->id}}">
                     @endif
                     <div class="row">
                        <div class="col-sm-6">
                            <label>Nome do Modelo</label>
                            <input type="name" class="form-control" name="Nome" value="{{isset($Registro) ? $Registro->Nome : '' }}">
                        </div>
                        <div class="col-sm-6">
                            <label>Tipo do Modelo</label>
                            <select name="TPModelo" class="form-control">
                                <option value="">Selecione</option>
                                <option value="Organizadores" {{isset($Registro) && $Registro->TPModelo == "Organizadores" ? 'selected' : '' }}>Organizadores</option>
                                <option value="Apresentadores" {{isset($Registro) && $Registro->TPModelo == "Apresentadores" ? 'selected' : '' }}>Apresentadores</option>
                                <option value="Telespectadores" {{isset($Registro) && $Registro->TPModelo == "Telespectadores" ? 'selected' : '' }}>Telespectadores</option>
                                <option value="Avaliador de Sessão" {{isset($Registro) && $Registro->TPModelo == "Avaliador de Sessão" ? 'selected' : '' }}>Avaliador de Sessão</option>
                                <option value="Moderador de Sessão" {{isset($Registro) && $Registro->TPModelo == "Moderador de Sessão" ? 'selected' : '' }}>Moderador de Sessão</option>
                                <option value="Telespectador de Palestra" {{isset($Registro) && $Registro->TPModelo == "Telespectador de Palestra" ? 'selected' : '' }}>Telespectador de Palestra</option>     
                                <option value="Palestrante" {{isset($Registro) && $Registro->TPModelo == "Palestrante" ? 'selected' : '' }}>Palestrante</option>    
                            </select>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                            <label>Modelo</label>
                            <textarea name="DSModelo" class="form-control" placeholder="Exemplo: o {organizador} assistiu o {evento} | com carga horaria de | 40 Horas">{{isset($Registro) ? $Registro->DSModelo : '' }}</textarea>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                            <label>Arquivo do Modelo</label>
                            <input type="file" class="form-control" onchange="displaySelectedImage(event, 'selectedModelo')" name="Arquivo" accept="image/*">
                            <input type="hidden" name="oldModelo" value="{{isset($Registro) ? $Registro->Arquivo : ''}}">
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <img src="{{isset($Registro) ? url('storage/modelos/'.$Registro->Arquivo) : ''}}" width="100%" height="500px" id="selectedModelo">
                        </div>
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
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header bg-fr text-white">
                    Regras
                </div>
                <div class="card-body" style="height: calc(100vh - 150px); overflow-y:scroll">
                    <dl>
                        <dt>Regras Gerais</dt>
                        <dd>
                            <ul>
                                <li>
                                    AO QUEBRAR UMA LINHA COLOQUE "|" SEM ASPAS, E COM ESPAÇAMENTO 
                                </li>
                            </ul>
                        </dd>
                        <dt>Organizadores</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{organizador}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do organizador,
                                    lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento
                                </li>
                            </ul>
                        </dd>
                        <dt>Apresentadores</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{apresentador}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do apresentador.
                                </li>
                                <li>lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento</li>
                                <li>Coloque <strong>{submissao}</strong> para que o sistema substitua pelo titulo da submissão</li>
                                <li>Coloque <strong>{autores}</strong> e <strong>{palavraschave}</strong> onde o sistema substituirá os respectivos itens</li>
                            </ul>
                        </dd>
                        <dt>Telespectadores</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{telespectador}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do telespectador.
                                </li>
                                <li>lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento</li>
                            </ul>
                        </dd>
                        <dt>Avaliador de Sessão</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{avaliadorsessao}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do avaliador.
                                </li>
                                <li>lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento</li>
                            </ul>
                        </dd>
                        <dt>Moderador de Sessão</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{moderador}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do moderador.
                                </li>
                                <li>lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento</li>
                            </ul>
                        </dd>
                        <dt>Telespectador de Palestra</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{telespectadorpalestra}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do telespectador.
                                </li>
                                <li>Coloque <strong>{palestra}</strong> onde quiser, para que o sistema substitua pelo nome da palestra, ou das palestras que o aluno assistiu, irá emitr de todas as palestras automaticamente</li>
                                <li>lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento</li>
                            </ul>
                        </dd>
                        <dt>Palestrantes</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{palestrante}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do palestrante.
                                </li>
                                <li>Coloque <strong>{palestra}</strong> onde quiser, para que o sistema substitua pelo nome da palestra, ou das palestras que o palestrante ministrou, irá emitr de todas as palestras automaticamente</li>
                                <li>lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento</li>
                            </ul>
                        </dd>
                        <dt>Avaliador de Trabalhos</dt>
                        <dd>
                            <ul>
                                <li>
                                    Coloque <strong>{avaliador}</strong> onde quiser ao criar o modelo, o sistema irá substituir pelo nome do Avaliador.
                                </li>
                                <li>lembrando que em todos os modelos, coloque <strong>{evento}</strong> onde for citar o evento, o sistema irá substituir pelo nome do evento</li>
                            </ul>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <script>
        alert("Atenção, Ler todas as Regras Antes de Criar um Modelo, de acordo com o publico, no campo 'Tipo de Modelo' ")
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