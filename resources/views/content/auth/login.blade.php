<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="Al Aziz Institute has a rich history of excellence in education and community service.">
    <meta name="author" content="Aliza Ali">
    <title>Login - Al Aziz Institute</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo_without_bg.png') }}" type="image/x-icon">


    @include('includes.links')
</head>

<body class="app app-login p-0">
    <div class="row g-0 app-auth-wrapper">
        <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
            <div class="d-flex flex-column align-content-end">
                <div class="app-auth-body mx-auto">
                    <div class="app-auth-branding mb-4"><a class="app-logo" href="#"><img class="logo-icon me-2"
                                src="{{ asset('assets/images/logo_without_bg.png') }}" alt="logo"></a></div>
                    <h2 class="auth-heading text-center mb-5">Log in to Admin Panel</h2>
                    <div class="auth-form-container text-start">
                        <form class="auth-form login-form" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="email mb-3">
                                <label class="sr-only" for="email">Email</label>
                                <input id="signin-email" name="email" type="email" class="form-control signin-email"
                                    placeholder="Email address">
                                @if ($errors->has('email'))
                                    <div class="text-danger small">{{ $errors->first('email') }}</div>
                                @endif
                            </div><!--//form-group-->
                            <div class="password mb-3">
                                <label class="sr-only" for="password">Password</label>
                                <input id="signin-password" name="password" type="password"
                                    class="form-control signin-password" placeholder="Password">
                                @if ($errors->has('password'))
                                    <div class="text-danger small">{{ $errors->first('password') }}</div>
                                @endif
                                @if ($errors->has('invalid'))
                                    <div class="text-danger small my-4 text-center">{{ $errors->first('invalid') }}
                                    </div>
                                @endif
                                <div class="extra mt-3 row justify-content-between">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="RememberPassword">
                                            <label class="form-check-label" for="RememberPassword">
                                                Remember me
                                            </label>
                                        </div>
                                    </div><!--//col-6-->
                                    <div class="col-6">
                                        <div class="forgot-password text-end">
                                            <a href="{{ route('forgot') }}">Forgot password?</a>
                                        </div>
                                    </div><!--//col-6-->
                                </div><!--//extra-->
                            </div><!--//form-group-->
                            <div class="text-center">
                                <button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Log
                                    In</button>
                            </div>
                        </form>
                    </div><!--//auth-form-container-->

                </div><!--//auth-body-->

                @include('includes.footer')
            </div><!--//flex-column-->
        </div><!--//auth-main-col-->
        <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
            <div class="auth-background-holder">
            </div>
            <div class="auth-background-mask"></div>
            <div class="auth-background-overlay p-3 p-lg-5">
                <div class="d-flex flex-column align-content-end h-100">
                    <div class="h-100"></div>
                </div>
            </div><!--//auth-background-overlay-->
        </div><!--//auth-background-col-->

    </div><!--//row-->

    @include('includes.scripts')
</body>

</html>
