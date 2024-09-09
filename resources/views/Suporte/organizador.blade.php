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
                            <dt>Cadastro do Evento</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Eventos</strong> > <strong>Adicionar</strong>
                                    </li>
                                    <li>
                                        O cadastro de eventos possui todas as informações necessárias sobre o evento, incluindo a personalização de cada site. Todos os campos do cadastro são obrigatórios, exceto a personalização, que é opcional no campo "Personalizar Site".
                                    </li>
                                    <li>
                                        Ao final do cadastro, clique no botão <strong>Salvar</strong>.
                                    </li>
                                    <li>
                                        Após cadastrar um evento, vá até a <strong>Listagem de Eventos</strong>. Escolha o evento desejado e clique em <strong>Abrir</strong> para visualizar o cadastro. Nessa página, estarão disponíveis todos os dados do evento, incluindo modalidades de submissão, categorias de inscrição e opções de personalização do site para edição.
                                    </li>
                                    <li>
                                        "O Tipo de Atividade" Deverá Ser Preenchido com o Nome da Atividade (Palestra,Workshop, ou o Que Preferirem)
                                    </li>
                                </ul>
                            </dd>
                            <dt>Cadastrar Apresentações</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Apresentações</strong> > <strong>Adicionar</strong>
                                    </li>
                                    <li>
                                        Ali estarão os dados para cadastro e a listagem dos trabalhos aprovados, com a possibilidade de filtrar por categoria de submissão. As submissões listadas estão disponíveis para cadastro. Portanto, se ao cadastrar uma atividade não aparecer nenhuma submissão, isso pode ocorrer porque não houve nenhuma submissão cadastrada, nenhuma foi aprovada, ou todas já estão cadastradas em outras atividades. Uma submissão só pode ser apresentada em uma sala.
                                    </li>
                                    <li>
                                        Caso queira mudar a submissão de atividade, na listagem de atividades, marque a caixinha "Mudar Apresentações?" e ela ficará disponível para cadastro em outras atividades.
                                    </li>                                    
                                </ul>
                            </dd>
                            <dt>Cadastrar Atividades</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Atividades</strong> > <strong>Adicionar</strong>
                                    </li>
                                    <li>
                                        Para Cadastrar uma Atividade, e Necessário ir em Coordenadores e Adicionar um Coordenador, para isso vá em Coordenadores > Adicionar
                                    </li>                            
                                    <li>
                                        Após isso Vá em Atividades > Adicionar e Preencha os Campos, Incluindo o Coordenador Pré-Cadastrado.
                                    </li>
                                    <li>
                                        As Atividades e Coordenadores Tambem Poderão ser Editados, Vá em Abrir, Nas Respectivas Listagens.
                                    </li>
                                    <li>
                                        Para Marcar a Presença do Usuário(Avalialiador,Organizador,Inscrito) Vá em Abrir, 
                                    </li>
                                </ul>
                            </dd>
                            <dt>Inscrever Alunos</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Inscrições</strong> > <strong>Adicionar</strong>
                                    </li>
                                    <li>
                                        Após isso, Preencha os Campos do Aluno, os Dados de Acesso São Gerados Automaticamente e Enviados ao Email Preenchido
                                    </li>
                                    <li>
                                        as Inscrições Tambem Poderão ser Editadas, Vá em Abrir na Listagem, Caso Queira Mudar A Senha do Aluno, Marque "Alterar Senha (A Nova Senha será enviada via Email)"
                                    </li>
                                </ul>
                            </dd>
                            <dt>Cadastrar Avaliador</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Avaliadores</strong> > <strong>Cadastrar</strong>
                                    </li>
                                    <li>
                                        Após isso, Preencha os Campos do Avaliador, os Dados de Acesso São Gerados Automaticamente e Enviados ao Email Preenchido
                                    </li>
                                    <li>
                                        os Avaliadores Tambem Poderão ser Editados, Vá em Abrir na Listagem, Caso Queira Mudar A Senha do Avaliador, Marque "Alterar Senha"
                                    </li>
                                    <li>
                                        os Avaliadores são Cadastrados Apenas em um ùnico evento, Caso queira Troca-lo de Evento, Faça Logout, Logue denovo, entre no evento e Vá em Avaliadors > Adicionar, que ele será automaticamente Puxado para o Evento Atual, a Senha Será a Mesma
                                    </li>
                                </ul>
                            </dd>
                            <dt>Cadastrar Organizador</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Organizadores</strong> > <strong>Cadastrar</strong>
                                    </li>
                                    <li>
                                        Após isso, Preencha os Campos do Organizador, os Dados de Acesso São Gerados Automaticamente e Enviados ao Email Preenchido
                                    </li>
                                    <li>
                                        os Organizadores Tambem Poderão ser Editados, Vá em Abrir na Listagem, Caso Queira Mudar A Senha do Organizador, Marque "Alterar Senha"
                                    </li>
                                    <li>
                                        Diferente dos Avaliadores, os Organizadores poderão ser Cadastrados em Varios Eventos, Para Adiciona-lo em um Outro Evento, o Organizador Cadastrante deve Fazer logout, Entrar no Evento, ir em Organizadores > Adicionar, Automátocamente Cadastrado no Evento Atual, a Senha Será a Mesma
                                    </li>
                                </ul>
                            </dd>
                            <dt>Cadastrar Submissões</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Submissões</strong> > <strong>Cadastrar</strong>
                                    </li>
                                    <li>
                                        Após isso, Preencha os Campos da Submissão
                                    </li>
                                    <li>
                                        As Submissões tambem poderão ser Alteradas, Vá em Editar na Listagem.
                                    </li>
                                    <li>
                                        A Submissão Ficará Disponivel a Todos os Alunos, Cada aluno poderá Submeter 1 Submissão por Evento, o Inscrito Deverá Ficar Atento aos Prazos.
                                    </li>
                                    <li>
                                        Ao Realizar a Submissão, A Entrega Ficará Disponivel em Submissões > Vá em Abrir na Submissão > Entregues, Alí Poderá Designar um Avaliador para Corrigi-la ou Você mesmo Corrigir,
                                        o Organizador Poderá Alterar Qualquer Dado da Submissão, Assim como sua categoría
                                    </li>
                                    <li>
                                        Ao Cadastrar uma Submissão, o Evento Não Poderá ter duas Submissões da Mesma Categoría, para Evitar Conflitos. 
                                    </li>
                                </ul>
                            </dd>
                            <dt>Formulários</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Formulários</strong> > <strong>Cadastrar</strong>
                                    </li>
                                    <li>
                                        Após isso, Escolha os Tipos de Pergunta, que a o Sistema Incrementará as Perguntas
                                    </li>
                                    <li>
                                        os Formulários Ficarão Disponiveis no Dashboard de Cada Inscrito e Avaliador do Evento, o Organizador tambem poderá Responde-los, Basta ir em 'Visualizar'
                                    </li>
                                    <li>
                                        As Respostas Estarão Disponiveis em Formato de Gráficos para os Organizadores, e eles poderão tambem Exportar as Respostas por XLSX em 'Exportar Respostas'
                                    </li>
                                    <li>
                                        o Organizador poderá editar o Formulário em 'Abrir' na listagem
                                    </li>
                                </ul>
                            </dd>
                            <dt>Certificados</dt>
                            <dd>
                                <ul>
                                    <li>
                                        Vá em <strong>Certificados</strong> > <strong>Modelos</strong> > <strong>Adicionar</strong>
                                    </li>
                                    <li>
                                        Após isso, Siga o Tutorial Disponível na Parte Lateral.
                                    </li>
                                    <li>
                                        Certificados do Tipo "Apresentador","Telespectador de Palestra" somente Funcionarão com Inscritos que <strong>Respectivamente</strong> Submeteram Trabalhos e Assistiram Palestras
                                    </li>
                                    <li>
                                        Certificados do Tipo "Telespectador de Palestra", Serão Emitidos um Certificado por Cada Palestra que o Usuário Assistiu no Evento
                                    </li>
                                    <li>
                                        Certificados do Tipo "Palestrante" Serão Emitidos Para Cada "Palestra" ou "Workshop" que o Coordenador Coordenou no Evento
                                    </li>
                                    <li>
                                        Na mesma Aba 'Modelos' o Modelo Poderá ser Editado, ou Excluido, Porem Não Recomendo a Exclusão Caso já Exista um Usuário Utilizando esse Modelo, em breve será implementada uma Funcionalidade que Bloqueia essa Ação
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