<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="Al Aziz Institute has a rich history of excellence in education and community service.">
    <meta name="author" content="Aliza Ali">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo_without_bg.png') }}" type="image/x-icon">
    <title>Al Aziz Institute</title>
    <style>
        .app-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 93vh;
        }

        .app-wrapper>.app-wrapper-content {
            flex: 1;
        }
    </style>

    @include('includes.links')
</head>

<body class="app">
    @include('includes.header')

    <div class="app-wrapper">
        <div class="app-wrapper-content">
            @yield('content')
        </div>
        @include('includes.footer')
    </div>

    @include('includes.scripts')
</body>

</html>
