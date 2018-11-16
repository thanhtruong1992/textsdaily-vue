<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>App - @yield('title')</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/validation.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/notification.js') }}"></script> --}}

    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>
<body>
    <div id="root">
        <router-view></router-view>
        {{-- <router-view name='app'></router-view>
        <router-view name='login'></router-view> --}}
        {{-- @include("notification")
        <div class="container-fluid bg-login">
            <div class="row height-full">
                @yield('content')
            </div>
        </div> --}}
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>