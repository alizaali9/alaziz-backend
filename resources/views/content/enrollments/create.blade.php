@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Add Enrollments</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('store.enrollment') }}" method="POST"
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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="w-50 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="student">
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->roll_no }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('student'))
                                <div class="text-danger small">{{ $errors->first('student') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="course">
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('course'))
                                <div class="text-danger small">{{ $errors->first('course') }}</div>
                            @endif
                        </div>

                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Add Enrollment
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
