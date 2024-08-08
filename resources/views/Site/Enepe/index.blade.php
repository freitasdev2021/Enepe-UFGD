<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('Site/css/style.css')}}">
    <title>Inscrições: Enepe 2024</title>
</head>
<body>
    <main>
        <nav class="nav-top">
            <a href="" id="logo"><img src="img/logo.png" width="300px" height="150px"></a>
            <a href="" id="login">Login/Cadastro</a>
        </nav>
        <br>
        <header>
            <div class="capa">
                <img src="img/capateste.jpg">
            </div>
        </header>
        <br>
        <article class="evento">
            <div>
                <h1>Evento de Teste da enelpem ufgd</h1>
                <p><i class='bx bxs-calendar'>16/11</i></p>
                <p><i class='bx bxs-camera-movie' >Evento Online</i></p>
            </div>
            <div class="botao">
                <a href="">Fazer Inscrição</a>
            </div>
        </article>
        <br>
        <article class="sobre">
            <h1 align="center">Sobre o Evento</h1>
            <p>
                No período de 06 a 10 de novembro de 2023, a Universidade Estadual de Mato Grosso do Sul (UEMS) e a Universidade Federal da Grande Dourados, realizará o IX ENEPEX / XIII EPEX – UEMS E XVII ENEPE – UFGD (Encontro de Ensino, Pesquisa e Extensão).

                O tema do evento deste ano será “O papel e os desafios da Universidade Pública na defesa do Estado democrático de direito".

                O evento será totalmente online e gratuito.

                A inscrição será exclusivamente pelo site do evento, com prazo até a data da respectiva programação.

                Para se inscrever, o participante deverá primeiramente fazer um cadastro pessoal na plataforma Even3. Quem já possuir, fica dispensado desse procedimento.

                Antes de submetê-lo, é indispensável que o participante leia atentamente as normas de submissão da respectiva modalidade (Ensino, Pesquisa, Extensão ou Pós-Graduação).

                A apresentação do trabalho será exclusivamente online na própria plataforma do evento e terá template de uso obrigatório, que será disponibilizado em momento oportuno.
            </p>
        </article>
        <br>
        <article class="sobre">
            <h1 align="center">Inscrições</h1>
            <h2 align="center">
                as Inscrições vão de 10 a 15 de Agosto de 2024 na Plataforma FR Academy
            </h2>
        </article>
        <br>
        <article class="sobre subms">
            <h1 align="center">Submissões</h1>
            <h2 align="center">
                as Submissões vão de 20 a 30 de Agosto de 2024
            </h2>
            <br>
            <div class="row d-flex justify-content-center">
                <div class="col-sm-11 subs">
                    @foreach($Submissoes as $s)
                    <a href="{{url('storage/regras_submissao/' . $s->Regras)}}" class="btn btn-success" target="_blank">{{$s->Categoria}}</a>
                    @endforeach
                </div>
            </div>
            <br>
        </article>
        <br>
        <article class="sobre subms">
            <h1 align="center">Atividades e Palestras</h1>
            <div class="row salas">
                @foreach($Palestras as $p)
                <div class="card sala" style="width: 18rem;">
                    <img src="{{url('storage/palestrantes/'.$p->Foto)}}" class="card-img-top" alt="...">
                    <div class="card-body">
                      <h5 class="card-title">{{$p->Titulo}}</h5>
                      <br>
                      <h6>{{$p->Nome}}</h6>
                      <p class="card-text">{{$p->Palestra}}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <br>
        </article>
        <br>
    </main>
    <script src="{{asset('Site/js/script.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>