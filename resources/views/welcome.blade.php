<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>RENEWABLE ENERGY - ADMIN TOOL</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
            .top-left_1 {
                position: absolute;
                left: 30px;
                top: 65px;
            }
            .top-left_2 {
                position: absolute;
                left: 30px;
                top: 160px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 18px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .bgMain {
                background-image: url('{{ asset('icons/background_welcome.jpg') }}');
                background-position: center center;
                -webkit-background-size: cover;
                -moz-background-size: cover;
                -o-background-size: cover;
                background-size: cover;
            }
            .pageTitle {
                font-size: 60px;
                font-weight: bold;
                color: black;
                text-shadow: 2px 2px #ffffff;
            }
            .pageSubtitle {
                font-size: 56px;
                font-weight: bold;
                color: black;
                text-shadow: 2px 2px #ffffff;
            }
            .footer_copyright {
                position: absolute;
                bottom: 0;
                font-weight: bold;
                color: black;
                width:100%;
            }
            .footer_logo {
                position: absolute;
                bottom: 0;
                width:100%;
            }
        </style>
    </head>
    <body class="bgMain">
        <div class="top-left_1 pageTitle">RENEWABLE ENERGY</div>
        <div class="top-left_2 pageSubtitle">FACILITIES DATABASE<br/>ADMIN TOOLS</div>

        <div class="top-right">
            @if (Route::has('login'))
            <span class="links">
                @auth
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}"><img src="{{ asset('icons/ic_user_login.png') }}" width="90"/></a>
                @endauth
            </span>
            @endif
        </div>
        <footer>
            <div class="footer_logo pl-3 pb-3">
                <img src="{{ asset('icons/logo_xl.png') }}" width="270" />
            </div>
            <div class="text-center footer_copyright pb-3">
                Copyright © <a href="#" target="_blank">Renewable - Energy</a> {{date("Y")}}
            </div>

        </footer>

        <!-- Bootstrap core JavaScript-->
        <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>
