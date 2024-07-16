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
                    <x-modulo nome="Eventos" icon="bx bxs-calendar-event" rota="profile.edit" endereco="Eventos"/>
                    <x-modulo nome="Palestras" icon="bx bx-user-pin" rota="profile.edit" endereco="Palestras"/>
                    <x-modulo nome="Palestrantes" icon="bx bxs-group" rota="profile.edit" endereco="Palestrantes"/>
                    <x-modulo nome="Avaliadores" icon="bx bxs-user-check" rota="profile.edit" endereco="Avaliadores"/>
                    <x-modulo nome="Submissões" icon="bx bx-task" rota="profile.edit" endereco="Submissoes"/>
                    <x-modulo nome="Certificados" icon="bx bxs-certification" rota="profile.edit" endereco="Certificados"/>
                    <x-modulo nome="Inscritos" icon="bx bx-group" rota="profile.edit" endereco="Inscritos"/>
                    @elseif(in_array(Auth::user()->tipo,[0,2]))
                    <x-modulo nome="Submissões" icon="bx bx-task" rota="profile.edit" endereco="Submissoes"/> 
                    <x-modulo nome="Certificados" icon="bx bxs-certification" rota="profile.edit" endereco="Certificados"/>
                    @elseif(in_array(Auth::user()->tipo,[0,3]))
                    <x-modulo nome="Submissões" icon="bx bx-task" rota="profile.edit" endereco="Submissoes"/> 
                    <x-modulo nome="Certificados" icon="bx bxs-certification" rota="profile.edit" endereco="Certificados"/>
                    <x-modulo nome="Eventos" icon="bx bxs-calendar-event" rota="profile.edit" endereco="Eventos"/>
                    <x-modulo nome="Palestras" icon="bx bx-user-pin" rota="profile.edit" endereco="Palestras"/>
                    <x-modulo nome="Atividades" icon="bx bxs-pencil" rota="profile.edit" endereco="Atividades"/>
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