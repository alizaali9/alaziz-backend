<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="Al Aziz Institute has a rich history of excellence in education and community service.">
    <meta name="author" content="Aliza Ali">
    <title>Delete Account - Al Aziz Institute</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo_without_bg.png') }}" type="image/x-icon">

    @include('includes.links')
</head>

<body class="app app-login p-0">
    <div class="row g-0 app-auth-wrapper">
        <div class="col-12 d-flex align-items-center col-md-7 col-lg-6 auth-main-col text-center p-5">
            <div class="d-flex flex-column align-content-end">
                <div class="app-auth-body mx-auto">
                    <div class="app-auth-branding mb-4"><a class="app-logo" href="#"><img class="logo-icon me-2"
                                src="{{ asset('assets/images/logo_without_bg.png') }}" alt="logo"></a></div>
                    <h2 class="auth-heading text-center mb-5">Delete Your Account</h2>
                    <p class="text-center mb-4">Please enter your roll number to delete your account.</p>
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
                    <div class="auth-form-container text-start">
                        <form class="auth-form delete-account-form" action="{{ route('delete.student') }}"
                            method="POST">
                            @csrf
                            @method("DELETE")
                            <div class="roll-number mb-3">
                                <label class="sr-only" for="roll_no">Roll Number</label>
                                <input id="roll-number" name="roll_no" type="text" class="form-control"
                                    placeholder="Enter your roll number">
                                @if ($errors->has('roll_number'))
                                    <div class="text-danger small">{{ $errors->first('roll_number') }}</div>
                                @endif
                            </div><!--//form-group-->
                            <div class="text-center">
                                <button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Delete My
                                    Account</button>
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
