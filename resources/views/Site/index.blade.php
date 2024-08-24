<!DOCTYPE html>
<html lang="pt-br">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
      <link rel="icon" type="image/x-icon" href="{{asset('img/fricon.ico')}}" />
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
      <style>
         .navbar a{
         color:white;
         }
         .salas{
         margin:10px;
         }
         .sala{
         padding:0;
         }
      </style>
      <title>{{$Evento->Titulo}}</title>
   </head>
   <body>
      <main>
         <!--MAIN-->
         <nav class="navbar navbar-expand-lg navbar-success bg-success">
            <div class="container-fluid">
               <a class="navbar-brand" href="#">{{$Evento->Titulo}}</a>
               <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarNav">
                  <ul class="navbar-nav">
                     <li class="nav-item">
                        <a class="nav-link" href="#">Login/Registro</a>
                     </li>
                  </ul>
               </div>
            </div>
         </nav>
         <!--CAROUSEL--->
         <div id="carouselExampleSlidesOnly" class="carousel slide p-2" data-bs-ride="carousel">
            <div class="carousel-inner">
               <div class="carousel-item active">
                  <img src="{{url('storage/Site/'.$Evento->Capa)}}" class="d-block w-100" alt="...">
               </div>
            </div>
         </div>
         <!----------->
         <!--ARTICLE 1-->
         <br>
         <div class="p-2">
            <div class="card">
               <div class="card-header">
                  <strong>Inscrições</strong>
               </div>
               <div class="card-body">
                  <h5 class="card-title">{{$Evento->Titulo}}</h5>
                  {{-- <p class="card-text">o Evento vai de {{date('d/m/Y',strtotime($Evento->Inicio))}} a {{date('d/m/Y',strtotime($Evento->Termino))}} e será Online</p> --}}
                  <h5 class="card-title">Sobre o Evento</h5>
                  <p class="card-text">{{$Evento->Descricao}}</p>
                  <div class="col-sm-12">
                     <h3 align="center">Inscreva-se na Plataforma</h3>
                     <hr>
                     <form id="form_acesso" action="{{ route('register') }}" method="POST">
                        @csrf
                        @method("POST")
                        <div class="form-outline mb-4">
                           <input type="name" name="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" required placeholder="Nome" />
                           @error('email')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                        </div>
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                           <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required placeholder="Email" />
                           @error('email')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                        </div>
                        <!-- Password input -->
                        <div class="form-outline mb-3">
                           <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required placeholder="Senha" />
                        </div>
                        <div class="form-outline mb-3">
                           <input type="password" name="password_confirmation" class="form-control form-control-lg @error('password') is-invalid @enderror" required placeholder="Confirme sua Senha" />
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                           <strong>
                           <a class="text-primary" href="{{route("login")}}" class="text-body">Já Está Cadastrado?</a>
                           </strong>
                        </div>
                        <div class="text-center text-lg-start mt-4 pt-2 col-sm-12">
                           <button type="submit" class="btn btn-lg bt-login btn-success">Registrar</button>
                        </div>
                        <br>
                        <span class="error"></span>
                        <!-- <strong class="btcliente"><a href='#'>Quero ser cliente(31 Dias Grátis sem compromisso)</a></strong> -->
                     </form>
                  </div>
               </div>
            </div>
            <br>
            <!--SUBMISSOES-->
            <br>
            <div class="card">
               <div class="card-header">
                  <strong>Submissões</strong>
               </div>
               <div class="card-body">
                  <h5 class="card-title text-center">Prazo para submissão: {{date('d/m/Y',strtotime($Evento->INISubmissao))}} - {{date('d/m/Y',strtotime($Evento->TERSubmissao))}}</h5>
                  <br>
                  @foreach($Submissoes as $s)
                  <div class="card">
                     <div class="card-body">
                        <a href="{{url('storage/regras_submissao/',$s->Regras)}}" target="_blank">{{$s->Categoria}}</a>
                     </div>
                  </div>
                  <br>
                  @endforeach
               </div>
            </div>
            <!--NORMAS DE APRESENTAÇÃO-->
            <br>
            <div class="card">
               <div class="card-header">
                     <strong>Normas de Apresentação</strong>
                  </div>
                  <div class="card-body">
                     <a href="{{url('storage/Site/'.$Evento->ModeloApresentacao)}}" download>Modelo de Apresentações</a>
                     <br>
                     <p class="card-text">
                        {{$Evento->Normas}}
                     </p>
                  </p>
               </div>
            </div>
            <!--Palestras-->
            <br>
            <div class="card">
               <div class="card-header">
                  <strong>Palestras</strong>
               </div>
               <div class="card-body">
                  <div class="row salas">
                     @foreach($Palestras as $p)
                     <div class="card sala" style="width: 18rem;">
                        <img src="{{url('storage/palestrantes/'.$p->Foto)}}" class="card-img-top" alt="...">
                        <div class="card-body">
                           <h5 class="card-title">{{$p->Titulo}}</h5>
                           <br>
                           <h6>{{$p->Nome}}</h6>
                           <p class="card-text">{{$p->Palestra}}</p>
                           <p class="card-text">{{date('d/m/Y',strtotime($p->created_at))}}  {{$p->Inicio}} - {{$p->Termino}}</p>
                        </div>
                     </div>
                     @endforeach
                  </div>
               </div>
            </div>
            <br>
            <!-----CONTATOS------>
            <div class="card">
               <div class="card-header">
                  <strong>Contatos</strong>
               </div>
               <div class="card-body">
                  <ul class="list-group">
                     @foreach(json_decode($Evento->Contatos,true) as $key =>$val)
                     @if(!empty($key))
                     <li class="list-group-item">{{$key ." - ".$val}}</li>
                     @endif
                     @endforeach
                  </ul>
                  <br>
               </div>
            </div>
            <!------------------->
         </div>
      </main>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
   </body>
</html>