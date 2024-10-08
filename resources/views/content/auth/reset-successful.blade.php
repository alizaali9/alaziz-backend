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

    <title>Reset Password - Al Aziz Institute</title>

    @include('includes.links')
</head>

<body class="app app-404-page">

    <div class="container mb-5">
        <div class="row">
            <div class="col-12 col-md-11 col-lg-7 col-xl-6 mx-auto">
                <div class="app-branding text-center mb-5">
                    <a class="app-logo" href="index.html"><img class="logo-icon me-2"
                            src="{{ asset('assets/images/logo_without_bg.png') }}" alt="logo"><span
                            class="logo-text">Al Aziz Institute</span></a>

                </div><!--//app-branding-->
                <div class="app-card p-5 text-center shadow-sm">
                    <h1 class="page-title mb-4">200<br><span class="font-weight-light">Password Reset Succesfully</span></h1>
                    {{-- <div class="mb-4">
                        This password reset link is invalid or has expired.
                    </div> --}}
                </div>
            </div><!--//col-->
        </div><!--//row-->
    </div><!--//container-->


    @include('includes.footer')
    @include('includes.scripts')
</body>

</html>
