@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Create Instructor</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('post.instructor') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (session('success'))
                        <div class="text-success text-center small pb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="w-50 mb-3">
                            <input id="name" name="name" type="text" class="form-control signin-email"
                                placeholder="Instructor Name">
                            @if ($errors->has('name'))
                                <div class="text-danger small">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <input id="email" name="email" type="email" class="form-control signin-email"
                                placeholder="Instructor Email">
                            @if ($errors->has('email'))
                                <div class="text-danger small">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row h-auto">
                        <div class="w-50 mb-3">
                            <textarea id="about" name="about" class="textarea form-control" placeholder="About the Instructor" rows="4"
                                cols="50"></textarea>
                            @if ($errors->has('about'))
                                <div class="text-danger small">{{ $errors->first('about') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3 textarea">
                            <textarea id="skills" name="skills" class="form-control" rows="4"
                                placeholder="Use Comma ',' to separate the skills"></textarea>
                            @if ($errors->has('skills'))
                                <div class="text-danger small">{{ $errors->first('skills') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-50 mb-3">
                            <input id="signin-password" name="password" type="password" class="form-control signin-password"
                                placeholder="Password">
                            @if ($errors->has('password'))
                                <div class="text-danger small">{{ $errors->first('password') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <input id="signin-password" name="password_confirmation" type="password" class="form-control signin-password"
                                placeholder="Confirm Password">
                            @if ($errors->has('password_confirmation'))
                                <div class="text-danger small">{{ $errors->first('password_confirmation') }}</div>
                            @endif
                        </div>
                        @if ($errors->has('invalid'))
                            <div class="text-danger small my-4 text-center">{{ $errors->first('invalid') }}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="w-50 mb-3">
                            <label for="picture" class="ps-2 pb-2">Upload Instructor Picture</label>
                            <input id="picture" name="picture" type="file" class="form-control signin-email"
                                placeholder="Upload Picture">
                            @if ($errors->has('picture'))
                                <div class="text-danger small">{{ $errors->first('picture') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Create Instructor
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
