@extends('layouts.app')

@section('content')
<body className='snippet-body'>
    <body classname="snippet-body" id="body-pd" class="body-pd" cz-shortcut-listen="true">
       <header class="header bg-fr" id="header">
      
       </header>
       <div class="l-navbar show" id="nav-bar">
          <nav class="nav" >
             <div>
                <a href="{{route('dashboard')}}" class="nav_logo"><i class='bx bxs-calendar-event text-white'></i></i><span class="nav_logo-name">FR Academy</span> </a>
                <div class="nav_list">
                    @if(in_array(Auth::user()->tipo,[0,1]))
                    <x-modulo nome="Eventos" icon="bx bxs-calendar-event" rota="Eventos/index" endereco="Eventos"/>
                    <x-modulo nome="Palestras" icon="bx bx-user-pin" rota="Palestras/index" endereco="Palestras"/>
                    <x-modulo nome="Palestrantes" icon="bx bxs-group" rota="Palestrantes/index" endereco="Palestrantes"/>
                    <x-modulo nome="Avaliadores" icon="bx bxs-user-check" rota="Avaliadores/index" endereco="Avaliadores"/>
                    <x-modulo nome="Submissões" icon="bx bx-task" rota="Submissoes/index" endereco="Submissoes"/>
                    <x-modulo nome="Certificados" icon="bx bxs-certification" rota="Certifica/index" endereco="Certifica"/>
                    <x-modulo nome="Site" icon="bx bxs-mouse-alt" rota="Site/index" endereco="Site"/>
                    @elseif(in_array(Auth::user()->tipo,[0,2]))
                    <x-modulo nome="Submissões" icon="bx bx-task" rota="Submissoes/index" endereco="Submissoes"/> 
                    <x-modulo nome="Atividades" icon="bx bxs-pencil" rota="Atividades/index" endereco="Atividades"/>
                    @elseif(in_array(Auth::user()->tipo,[0,3]))
                    @if(!Session::has('IDEvento'))
                    <x-modulo nome="Eventos" icon="bx bxs-calendar-event" rota="Eventos/index" endereco="Eventos"/>
                    @else
                    <x-modulo nome="Submissões" icon="bx bx-task" rota="Submissoes/index" endereco="Submissoes"/> 
                    <x-modulo nome="Certificados" icon="bx bxs-certification" rota="Certifica/index" endereco="Certifica"/>
                    <x-modulo nome="Palestras" icon="bx bx-user-pin" rota="Palestras/index" endereco="Palestras"/>
                    <x-modulo nome="Atividades" icon="bx bxs-pencil" rota="Atividades/index" endereco="Atividades"/>
                    @endif
                    @endif 
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
         windowHeight = $(window).height()
         $(".bari").css("height",windowHeight)
       </script>
 </body>
@endsection