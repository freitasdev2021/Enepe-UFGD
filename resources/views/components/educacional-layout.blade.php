@extends('layouts.app')

@section('content')
<body className='snippet-body'>
    <body classname="snippet-body" id="body-pd" class="body-pd" cz-shortcut-listen="true">
       <header class="header bg-fr" id="header">
      
       </header>
       <div class="l-navbar show" id="nav-bar">
          <nav class="nav" >
             <div>
                <a href="{{route('dashboard')}}" class="nav_logo"><i class='bx bxs-calendar-event text-white'></i></i><span class="nav_logo-name">FR Eventos</span> </a>
                <div class="nav_list">
                    @if(in_array(Auth::user()->tipo,[1]))
                     <x-Modulo nome="Eventos" icon="bx bxs-calendar-event" rota="Eventos/index" endereco="Eventos"/>
                     @if(Session::has('IDEvento'))
                        <x-Modulo nome="Atividades" icon="bx bx-user-pin" rota="Palestras/index" endereco="Palestras"/>
                        <x-Modulo nome="Coordenadores" icon="bx bxs-group" rota="Palestrantes/index" endereco="Palestrantes"/>
                        <x-Modulo nome="Avaliadores" icon="bx bxs-user-check" rota="Avaliadores/index" endereco="Avaliadores"/>
                        <x-Modulo nome="Submissões" icon="bx bx-task" rota="Submissoes/index" endereco="Submissoes"/>
                        <x-Modulo nome="Certificados" icon="bx bxs-certification" rota="Certifica/index" endereco="Certifica"/>
                        <x-Modulo nome="Organizadores" icon="bx bxs-user" rota="Organizadores/index" endereco="Organizadores"/>
                        <x-Modulo nome="Formulários" icon="bx bxs-dock-top" rota="Formularios/index" endereco="Formularios"/>
                     @endif
                    @elseif(in_array(Auth::user()->tipo,[2]))
                    <x-Modulo nome="Submissões" icon="bx bx-task" rota="Submissoes/index" endereco="Submissoes"/> 
                    <x-Modulo nome="Atividades" icon="bx bxs-pencil" rota="Atividades/index" endereco="Atividades"/>
                    @elseif(in_array(Auth::user()->tipo,[3]))
                     @if(!Session::has('IDEvento'))
                     <x-Modulo nome="Eventos" icon="bx bxs-calendar-event" rota="Eventos/index" endereco="Eventos"/>
                     @else
                     <x-Modulo nome="Submissões" icon="bx bx-task" rota="Submissoes/index" endereco="Submissoes"/> 
                     <x-Modulo nome="Atividades" icon="bx bx-user-pin" rota="Palestras/index" endereco="Palestras"/>
                     <x-Modulo nome="Apresentações" icon="bx bxs-pencil" rota="Atividades/index" endereco="Atividades"/>
                     @endif
                    @endif
                    <x-Modulo nome="Suporte" icon="bx bx-support nav_icon" rota="Suporte/index" endereco="Suporte"/>
                    <x-Modulo nome="Alterar Senha" icon="bx bxs-cog nav_icon" rota="Configuracoes/index" endereco="Configuracoes"/>
                    {{-- <a href="https://wa.me/5531983086235" class="nav_link" target="_blank">
                     <i class='bx bx-support nav_icon'></i>
                     <span class="nav_name">Suporte</span>
                    </a>  --}}
                </div>
             </div>
             <form action="{{route('logout')}}" method="POST">
               @csrf
               <button class="nav_link sair" type="submit"><i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Sair</span> </button>
             </form>
          </nav>
       </div>
       <!--Container Main start-->
       <div class="bari" style="margin-top:100px; margin-right:15px;">
          {{$slot}}
       </div>
       <!--Container Main end-->
       <script>
         // windowHeight = $(window).height()
         // $(".bari").css("height",windowHeight)
       </script>
 </body>
@endsection