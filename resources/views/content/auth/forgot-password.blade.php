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

<body class="app app-reset-password p-0">
    <div class="row g-0 app-auth-wrapper">
        <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center pt-3">
            <div class="d-flex flex-column align-content-end">
                <div class="app-auth-body mx-auto">
                    <div class="app-auth-branding mb-4"><a class="app-logo" href="#"><img class="logo-icon me-2"
                                src="{{ asset('assets/images/logo_without_bg.png') }}" alt="logo"></a></div>
                    <h2 class="auth-heading text-center mb-4">Password Reset</h2>


                    @if (session('success'))
                        <div class="text-success text-center small pb-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="text-danger text-center small pb-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="auth-intro mb-4 text-center">Enter your email address below. We'll email you a link to a
                        page where you can easily create a new password.</div>

                    <div class="auth-form-container text-left">

                        <form class="auth-form resetpass-form" action="{{ route('reset.send') }}" method="POST">
                            @csrf
                            <div class="email mb-3">
                                <label class="sr-only" for="email">Your Email</label>
                                <input id="reg-email" name="email" type="email" class="form-control login-email"
                                    placeholder="Your Email" required="required">
                            </div><!--//form-group-->
                            <div class="text-center">
                                <button type="submit" class="btn app-btn-primary btn-block theme-btn mx-auto">Reset
                                    Password</button>
                            </div>
                        </form>

                        <div class="auth-option text-center pt-5"><a class="app-link"
                                href="{{ route('login.show') }}">Back to Log in</a></div>
                    </div><!--//auth-form-container-->


                </div><!--//auth-body-->

                @include('includes.footer')
            </div><!--//flex-column-->
        </div><!--//auth-main-col-->
        <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
            <div class="auth-background-holder">
            </div>
            <div class="auth-background-mask"></div>
        </div><!--//auth-background-col-->

    </div><!--//row-->

    @include('includes.scripts')
</body>

</html>