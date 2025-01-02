<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Al Aziz Institute is dedicated to providing top-notch education and services.">
    <meta name="author" content="Aliza Ali">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo_without_bg.png') }}" type="image/x-icon">

    <title>500 - Internal Server Error | Al Aziz Institute</title>

    @include('includes.links')
</head>

<body class="app app-500-page">
    <div class="d-flex flex-column justify-content-between" style="min-height: 93vh">
        <div class="container mb-5">
            <div class="row">
                <div class="col-12 col-md-11 col-lg-7 col-xl-6 mx-auto">
                    <div class="app-branding text-center mb-5">
                        <a class="app-logo" href="{{ route('dashboard') }}">
                            <img class="logo-icon me-2" src="{{ asset('assets/images/logo_without_bg.png') }}"
                                alt="logo">
                            <span class="logo-text">Al Aziz Institute</span>
                        </a>
                    </div><!--//app-branding-->
                    <div class="app-card p-5 text-center shadow-sm">
                        <h1 class="page-title mb-4">500<br><span class="font-weight-light">Internal Server Error</span>
                        </h1>
                        <div class="mb-4">
                            Sorry, something went wrong on our end. Please try again later or contact support if the
                            issue
                            persists.
                        </div>
                        <a class="btn app-btn-primary" href="{{ route('dashboard') }}">Go back to Home</a>
                    </div>
                </div><!--//col-->
            </div><!--//row-->
        </div><!--//container-->

        @include('includes.footer')
    </div>
    @include('includes.scripts')
</body>

</html>
