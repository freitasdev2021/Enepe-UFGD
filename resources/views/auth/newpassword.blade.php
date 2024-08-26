<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Controller: Login</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link rel="icon" type="image/x-icon" href="img/fricon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
</head>
<body>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="{{asset('img/logo.png')}}"
                class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <h3 align="center">Recuperação de Senha</h3>
                <hr>
                @csrf
                @method("POST")
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
                <br>
                <form id="form_acesso" action="{{ route('PasswordReset/SendEmail') }}" method="POST">
                @csrf
                @method("POST")
                <!-- Email input -->
                <div class="form-outline mb-4">
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required placeholder="Email" />
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <strong>
                        <a class="text-primary" href="{{route("login")}}" class="text-body">Login</a>
                    </strong>
                </div>

                <div class="text-center text-lg-start mt-4 pt-2">
                    <button type="submit" class="btn btn-lg col-sm-12 bt-login">Recuperar Senha</button>
                </div>
                <br>
                <span class="error"></span>
                <!-- <strong class="btcliente"><a href='#'>Quero ser cliente(31 Dias Grátis sem compromisso)</a></strong> -->
                </form>
            </div>
            </div>
        </div>
    </section>
</body>
</html>