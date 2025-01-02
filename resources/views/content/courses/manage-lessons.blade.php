@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Manage Lessons of Course ({{ $course->name }})</h1>
                    <a type="button" class="btn app-btn-primary theme-btn mx-auto mt-2" target="_blank"
                        href="{{ route('courses.uploadContent', ['courseid' => $course->id]) }}">
                        Add Course Lessons
                    </a>
                </div>

                {{-- {{dd($lessons->coursePart->name)}} --}}
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <div class="app-search-box">
                                    <form class="app-search-form" method="GET"
                                        action="{{ route('courses.manageLessons', ['courseId' => $course->id]) }}">
                                        <input type="text" placeholder="Search..." name="search"
                                            value="{{ request('search') }}" class="form-control search-input">
                                        <button type="submit" class="btn search-btn" value="Search">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>
                                </div><!--//app-search-box-->
                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary"
                                    href="{{ route('lessons.download.csv', ['courseid' => $course->id, 'search' => request()->get('search')]) }}">
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-download me-1"
                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                                        <path fill-rule="evenodd"
                                            d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                                    </svg>
                                    Download CSV
                                </a>
                            </div>
                        </div><!--//row-->
                    </div><!--//table-utilities-->
                </div><!--//col-auto-->
            </div><!--//row-->

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
                <div class="text-danger text-center small pb-3">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</>
                    @endforeach
                </div>
            @endif
            <div id="success-msg" class="text-success text-center small pb-3">

            </div>
            <div class="tab-content" id="orders-table-tab-content">
                <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
                    <div class="app-card app-card-orders-table shadow-sm mb-5">
                        <div class="app-card-body">
                            <div class="table-responsive">
                                <table class="table app-table-hover mb-0 text-left">
                                    <thead>
                                        <tr>
                                            <th class="cell text-center">Lesson Name</th>
                                            <th class="cell text-center">Lesson Type</th>
                                            <th class="cell text-center">Lesson Part</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lessons as $lesson)
                                            <tr>
                                                <td class="cell text-center">{{ $lesson->title }}</td>
                                                <td class="cell text-center">{{ $lesson->type }}</td>
                                                <td class="cell text-center">{{ $lesson->coursePart->name }}</td>
                                                <td class="cell d-flex justify-content-center">

                                                    <form
                                                        action="{{ route('course-materials.moveUp', ['id' => $lesson->id]) }}"
                                                        method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success rounded-4"
                                                            {{ $loop->first ? 'disabled' : '' }}>
                                                            <i class="fa-solid text-white fa-arrow-up"></i>
                                                        </button>
                                                    </form>

                                                    <form
                                                        action="{{ route('course-materials.moveDown', ['id' => $lesson->id]) }}"
                                                        method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-secondary  rounded-4"
                                                            {{ $loop->last ? 'disabled' : '' }}>
                                                            <i class="fa-solid text-white fa-arrow-down"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                        class="btn app-btn-primary theme-btn edit-lesson-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editLessonModal"
                                                        data-id="{{ $lesson->id }}" data-name="{{ $lesson->title }}"
                                                        data-url="{{ $lesson->url }}"
                                                        data-partId="{{ $lesson->part_id }}"
                                                        data-type="{{ $lesson->type }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-pen-fill"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                                        </svg>
                                                    </button>
                                                    <div class="ps-2">
                                                        <form
                                                            action="{{ route('lessons.delete', ['lessonId' => $lesson->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn app-btn-danger theme-btn mx-auto">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" fill="currentColor"
                                                                    class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div><!--//table-responsive-->
                        </div><!--//app-card-body-->
                    </div><!--//app-card-->
                </div><!--//tab-pane-->
            </div><!--//tab-content-->
        </div><!--//container-fluid-->
    </div><!--//app-content-->

    <!-- Edit Lesson Modal -->
    <div class="modal fade" id="editLessonModal" tabindex="-1" aria-labelledby="editLessonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLessonModalLabel">Edit Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="edit-lesson-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="lesson-id">
                        <input type="hidden" name="course_id" id="lesson-course-id">

                        <div class="mb-3">
                            <label for="lesson-name" class="form-label">Lesson Name</label>
                            <input type="text" class="form-control" id="lesson-name" name="title" required>
                            @if ($errors->has('title'))
                                <div class="text-danger small">{{ $errors->first('title') }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="lesson-part" class="form-label">Part</label>
                            <select class="form-select form-select-sm" id="lesson-part" name="part">
                                <option value="">Select Part</option>
                                @foreach ($courseParts as $coursePart)
                                    <option value="{{ $coursePart->id }}">{{ $coursePart->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('part'))
                                <div class="text-danger small">{{ $errors->first('part') }}</div>
                            @endif
                        </div>
                        <script>
                            function toggleLessonInput(value) {
                                var fileInputContainer = document.getElementById('file-input-container');
                                var urlInputContainer = document.getElementById('url-input-container');
                                if (value === 'file') {
                                    fileInputContainer.classList.remove('d-none');
                                    urlInputContainer.classList.add('d-none');
                                } else if (value === 'url') {
                                    fileInputContainer.classList.add('d-none');
                                    urlInputContainer.classList.remove('d-none');
                                } else {
                                    fileInputContainer.classList.add('d-none');
                                    urlInputContainer.classList.add('d-none');
                                }
                            }
                        </script>
                        <div class="mb-3">

                            <label for="lesson-type" class="form-label">Lesson Type</label>
                            <select id="lesson-type" class="form-select form-select-sm" name="content_type"
                                onchange="toggleLessonInput(this.value)">
                                <option value="">Choose the Lesson Type</option>
                                <option value="file">Upload File</option>
                                <option value="url">Enter URL</option>
                            </select>
                            @if ($errors->has('content_type'))
                                <div class="text-danger small">{{ $errors->first('content_type') }}</div>
                            @endif
                        </div>

                        <div id="file-input-container" class="mb-3 d-none">
                            <input id="lesson-file" name="lesson" type="file" class="form-control">
                            @if ($errors->has('lesson'))
                                <div class="text-danger small">{{ $errors->first('lesson') }}</div>
                            @endif
                        </div>

                        <div id="url-input-container" class="mb-3 d-none">
                            <input id="lesson-url" name="lesson_url" type="text" class="form-control"
                                placeholder="Enter Lesson URL">
                            @if ($errors->has('lesson_url'))
                                <div class="text-danger small">{{ $errors->first('lesson_url') }}</div>
                            @endif
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn app-btn-secondary theme-btn"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn app-btn-primary theme-btn">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editLessonButtons = document.querySelectorAll('.edit-lesson-btn');
            var editLessonForm = document.getElementById('edit-lesson-form');
            var lessonIdInput = document.getElementById('lesson-id');
            var courseIdInput = document.getElementById('lesson-course-id');
            var lessonNameInput = document.getElementById('lesson-name');
            var lessonPartSelect = document.getElementById('lesson-part');
            var lessonTypeSelect = document.getElementById('lesson-type');
            var fileInputContainer = document.getElementById('file-input-container');
            var urlInputContainer = document.getElementById('url-input-container');
            var lessonFileInput = document.getElementById('lesson-file');
            var lessonUrlInput = document.getElementById('lesson-url');

            editLessonButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var id = this.getAttribute('data-id');
                    var name = this.getAttribute('data-name');
                    var partId = this.getAttribute('data-partId');
                    var type = this.getAttribute('data-type');
                    var url = this.getAttribute('data-url');

                    editLessonForm.action = '{{ route('lessons.update', ':id') }}'.replace(':id',
                        id);
                    lessonIdInput.value = id;
                    courseIdInput.value = '{{ $course->id }}';
                    lessonNameInput.value = name;
                    lessonPartSelect.value = partId;
                    lessonTypeSelect.value = (type == 'video' || type == 'pdf') ? 'file' : "url";
                    lessonUrlInput.value = type == 'url' ? url: null;

                    if (type == 'video' || type == 'pdf') {
                        fileInputContainer.classList.remove('d-none');
                        urlInputContainer.classList.add('d-none');
                    } else if (type === 'url') {
                        fileInputContainer.classList.add('d-none');
                        urlInputContainer.classList.remove('d-none');
                    } else {
                        fileInputContainer.classList.add('d-none');
                        urlInputContainer.classList.add('d-none');
                    }
                });
            });
        });
    </script>

@endsection
