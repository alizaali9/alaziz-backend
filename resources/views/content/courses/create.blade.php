@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Create Course</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('courses.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input name="created_by" type="hidden" value="{{ Auth::user()->id }}">
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
                    <div class="row">
                        <div class="w-50 mb-3">
                            <input id="name" name="name" type="text" class="form-control signin-email"
                                placeholder="Course Name">
                            @if ($errors->has('name'))
                                <div class="text-danger small">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <input id="language" name="language" type="text" class="form-control signin-email"
                                placeholder="Course Language">
                            @if ($errors->has('language'))
                                <div class="text-danger small">{{ $errors->first('language') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="description" name="description" class="textarea form-control" placeholder="Course Description"
                                rows="4" cols="50"></textarea>
                            @if ($errors->has('description'))
                                <div class="text-danger small">{{ $errors->first('description') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-50 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="level">
                                <option value="">Level of Course</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advance">Advance</option>
                            </select>
                            @if ($errors->has('level'))
                                <div class="text-danger small">{{ $errors->first('level') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <input id="price" name="price" type="number" class="form-control signin-email"
                                placeholder="Course Price">
                            @if ($errors->has('price'))
                                <div class="text-danger small">{{ $errors->first('price') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-100 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="sub_category">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('level'))
                                <div class="text-danger small">{{ $errors->first('level') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="demo" class="ps-2 pb-2">Upload your Demo Video</label>
                            <input id="demo" name="demo" type="file" class="form-control"
                                style="padding-top: 7px;" placeholder="Demo Video">
                            @if ($errors->has('demo'))
                                <div class="text-danger small">{{ $errors->first('demo') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="overview" name="overview" class="textarea form-control" placeholder="Course Overview" rows="4"
                                cols="50"></textarea>
                            @if ($errors->has('overview'))
                                <div class="text-danger small">{{ $errors->first('overview') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="outcome" name="outcome" class="textarea form-control" placeholder="Course Outcome" rows="4"
                                cols="50"></textarea>
                            @if ($errors->has('outcome'))
                                <div class="text-danger small">{{ $errors->first('outcome') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="requirement" name="requirement" class="textarea form-control" placeholder="Course Requirement"
                                rows="4" cols="50"></textarea>
                            @if ($errors->has('requirement'))
                                <div class="text-danger small">{{ $errors->first('requirement') }}</div>
                            @endif
                        </div>
                    </div>


                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Create Course
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
