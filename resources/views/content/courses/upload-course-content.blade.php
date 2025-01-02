@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start" style="height: 71vh">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Upload Course Content</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('courses.storeContent') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input name="course_id" type="hidden" value="{{ $courseid }}">
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
                        @foreach ($errors->all() as $error)
                            <div class="text-danger text-center small pb-3">
                                {{ $error }}
                            </div>
                        @endforeach
                    @endif

                    <div id="input-containers">
                        <div class="row justify-content-center">
                            <div class="w-50 mb-3">
                                <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control" name="part">
                                    <option value="">Select Part</option>
                                    @foreach ($courseParts as $coursePart)
                                        <option value="{{ $coursePart->id }}">{{ $coursePart->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('part'))
                                    <div class="text-danger small">{{ $errors->first('part') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="w-50 mb-3">
                                <input id="title" name="title" type="text" class="form-control signin-email" placeholder="Lesson Name">
                                @if ($errors->has('title'))
                                    <div class="text-danger small">{{ $errors->first('title') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="w-50 mb-3">
                                <select id="content-type" class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control" name="content_type" onchange="toggleContent(this.value)">
                                    <option value="">Choose the Lesson Type</option>
                                    <option value="file">Upload File</option>
                                    <option value="url">Enter URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="w-50 mb-3 d-none" id="file-input-container">
                                <input id="file-lesson" name="lesson_file" type="file" class="form-control signin-email" style="padding-block: 7px;" placeholder="Upload Lesson">
                                @if ($errors->has('lesson_file'))
                                    <div class="text-danger small">{{ $errors->first('lesson_file') }}</div>
                                @endif
                            </div>
                            <div class="w-50 mb-3 d-none" id="url-input-container">
                                <input id="url-lesson" name="lesson_url" type="text" class="form-control signin-email" placeholder="Enter Lesson URL">
                                @if ($errors->has('lesson_url'))
                                    <div class="text-danger small">{{ $errors->first('lesson_url') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleContent(value) {
            const fileInputContainer = document.getElementById('file-input-container');
            const urlInputContainer = document.getElementById('url-input-container');
            const fileInput = document.getElementById('file-lesson');
            const urlInput = document.getElementById('url-lesson');

            if (value === 'file') {
                fileInputContainer.classList.remove('d-none');
                urlInputContainer.classList.add('d-none');
                fileInput.disabled = false;
                urlInput.disabled = true;
            } else if (value === 'url') {
                fileInputContainer.classList.add('d-none');
                urlInputContainer.classList.remove('d-none');
                fileInput.disabled = true;
                urlInput.disabled = false;
            } else {
                fileInputContainer.classList.add('d-none');
                urlInputContainer.classList.add('d-none');
                fileInput.disabled = true;
                urlInput.disabled = true;
            }
        }
    </script>
@endsection
