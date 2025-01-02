@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Create Quiz</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('store.quiz') }}" method="POST"
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
                        <div class="w-50 mb-3">
                            <input id="name" name="name" type="text" class="form-control signin-email"
                                placeholder="Quiz Name">
                            @if ($errors->has('name'))
                                <div class="text-danger small">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <input id="timelimit" name="timelimit" type="number" class="form-control signin-email"
                                placeholder="Time to complete the Quiz (in min)">
                            @if ($errors->has('timelimit'))
                                <div class="text-danger small">{{ $errors->first('timelimit') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-50 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="sub_category">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('sub_category'))
                                <div class="text-danger small">{{ $errors->first('sub_category') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="status">
                                <option value="">Choose Status</option>
                                <option value="1">Active</option>
                                <option value="0">Not Active</option>
                            </select>
                            @if ($errors->has('status'))
                                <div class="text-danger small">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="thumbnail" class="ps-2 pb-2">Upload thumbnail</label>
                            <input id="thumbnail" name="thumbnail" type="file" class="form-control"
                                style="padding-top: 7px;" placeholder="Upload thumbnail">
                            @if ($errors->has('thumbnail'))
                                <div class="text-danger small">{{ $errors->first('thumbnail') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="row h-auto">
                        <div class="col-4 mb-3">
                            <input id="price" name="price" type="number" class="form-control signin-email"
                                placeholder="Quiz Price">
                            @if ($errors->has('price'))
                                <div class="text-danger small">{{ $errors->first('price') }}</div>
                            @endif
                        </div>
                        <div class="col-4 mb-3">
                            <input id="discount" name="discount" type="number" class="form-control signin-email"
                                placeholder="Discount">
                            @if ($errors->has('discount'))
                                <div class="text-danger small">{{ $errors->first('discount') }}</div>
                            @endif
                        </div>
                        <div class="col-4 mb-3">
                            <input id="tries" name="tries" type="number" class="form-control signin-email"
                                placeholder="How many times student can try?">
                            @if ($errors->has('tries'))
                                <div class="text-danger small">{{ $errors->first('tries') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="file" class="ps-2 pb-2">Upload Quiz Excel File</label>
                            <input id="file" name="file" type="file" class="form-control"
                                style="padding-top: 7px;" placeholder="Upload Quiz Excel File">
                            @if ($errors->has('file'))
                                <div class="text-danger small">{{ $errors->first('file') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Create Quiz
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
