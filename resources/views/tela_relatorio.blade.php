<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relatório espelho de ponto</title>
    <link rel="icon" href="{{env('LINK_MINI_LOGO')}}" sizes="32x32">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="{{asset('css/tela.css')}}">


    <!-- Styles -->
    <style>
       
    </style>
</head>
<body>
    <form class="form" method="post" enctype="multipart/form-data">
        @csrf
        <img src="{{env('LINK_LOGO')}}" alt="Logo" class="logo"> <!-- Substitua pelo caminho do seu logo -->

        <p class="form-title">Escolha um arquivo para upload</p>

        @if ($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="input-container">
            <input type="file" name="file">
            <span></span>
        </div>
        <button type="submit" class="submit">
            Enviar
        </button>
    </form>
</body>
</html>
