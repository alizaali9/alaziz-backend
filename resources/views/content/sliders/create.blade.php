@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Upload Slider</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('sliders.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input name="created_by" type="hidden" value="{{ Auth::user()->id }}">
                    @if (session('success'))
                        <div class="text-success text-center small pb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="text-danger text-center small pb-3">
                            <p>Something went wrong</p>
                        </div>
                    @endif
                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="slider" class="ps-2 pb-2">Upload Slider Image</label>
                            <input id="slider" name="slider" type="file" class="form-control"
                                style="padding-top: 7px;" placeholder="Upload Slider Image">
                            @if ($errors->has('slider'))
                                <div class="text-danger small">{{ $errors->first('slider') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="sliderlink" class="ps-2 pb-2">Add Slider Link</label>
                            <input id="link" name="link" type="text" class="form-control"
                                style="padding-top: 7px;" placeholder="Add Slider Link">
                            @if ($errors->has('link'))
                                <div class="text-danger small">{{ $errors->first('link') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Upload
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
