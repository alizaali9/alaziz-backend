@extends('layout.layout')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Courses</h1>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <div class="app-search-box">
                                    <form class="app-search-form">
                                        <input type="text" placeholder="Search..." name="search"
                                            class="form-control search-input">
                                        <button type="submit" class="btn search-btn btn-primary" value="Search">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>
                                </div><!--//app-search-box-->
                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary" href="#">
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
                                            <th class="cell text-center">Course Name</th>
                                            <th class="cell text-center">Course Category</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($courses as $course)
                                            <tr>
                                                <td class="cell text-center">{{ $course->name }}</td>
                                                <td class="cell text-center">{{ $course->subcategory->name }}</td>
                                                <td class="cell d-flex justify-content-center">
                                                    <div>
                                                        <button type="button"
                                                            class="btn app-btn-primary theme-btn mx-auto edit-course-btn"
                                                            data-course='{
                                                                "id": "{{ $course->id }}",
                                                                "name": "{{ $course->name }}",
                                                                "language": "{{ $course->language }}",
                                                                "overview": "{{ $course->overview }}",
                                                                "description": "{{ $course->description }}",
                                                                "level": "{{ $course->level }}",
                                                                "sub_category": "{{ $course->subcategory->id }}",
                                                                "price": "{{ $course->price }}",
                                                                "outcome": "{{ $course->outcome }}",
                                                                "requirements": "{{ $course->requirements }}"}'>
                                                            Manage Course
                                                        </button>
                                                    </div>
                                                    <div class="ms-2">
                                                        <a type="button" class="btn app-btn-primary theme-btn mx-auto"
                                                            href="{{ route('update.courses.parts', ['id' => $course->id]) }}">
                                                            Manage Course Parts
                                                        </a>
                                                    </div>
                                                    <div class="ms-2">
                                                        <a type="button"
                                                            href="{{ route('courses.manageLessons', ['courseId' => $course->id]) }}"
                                                            class="btn app-btn-primary theme-btn mx-auto edit-quiz-btn">
                                                            Manage Course Lessons
                                                        </a>
                                                    </div>
                                                    <div class="ps-2">
                                                        <form
                                                            action="{{ route('courses.delete', ['id' => $course->id]) }}"
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
    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" id="edit-course-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="course-id">
                        <div class="mb-3">
                            <label for="course-name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="course-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="course-language" class="form-label">Language</label>
                            <input type="text" class="form-control" id="course-language" name="language" required>
                        </div>
                        <div class="mb-3">
                            <label for="course-overview" class="form-label">Overview</label>
                            <textarea class="form-control" id="course-overview" name="overview" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="course-description" class="form-label">Description</label>
                            <textarea class="form-control" id="course-description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="course-level" class="form-label">Level</label>
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="level">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advance">Advance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="course-level" class="form-label">Categories</label>
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="sub_category" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="course-price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="course-price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="course-outcome" class="form-label">Outcome</label>
                            <textarea class="form-control" id="course-outcome" name="outcome" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="course-requirements" class="form-label">Requirements</label>
                            <textarea class="form-control" id="course-requirements" name="requirements" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="w-100 mb-3">
                                <label for="thumbnail" class="ps-2 pb-2">Change Thumbnail Image</label>
                                <input id="thumbnail" name="thumbnail" type="file" class="form-control"
                                    style="padding-top: 7px;" placeholder="Thumbnail Image">
                            </div>
                        </div>
                        <div class="row">
                            <div class="w-100 mb-3">
                                <label for="demo" class="ps-2 pb-2">Change Demo Video</label>
                                <input id="demo" name="demo" type="file" class="form-control"
                                    style="padding-top: 7px;" placeholder="Demo Video">
                            </div>
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
            document.querySelectorAll('.edit-course-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const course = this.dataset.course;
                    const courseData = JSON.parse(course);

                    const updateUrl = `{{ url('courses/update') }}/${courseData.id}`;
                    document.getElementById('edit-course-form').action = updateUrl;

                    document.getElementById('course-id').value = courseData.id;
                    document.getElementById('course-name').value = courseData.name;
                    document.getElementById('course-language').value = courseData.language;
                    document.getElementById('course-overview').value = courseData.overview;
                    document.getElementById('course-description').value = courseData.description;
                    document.querySelector('select[name="level"]').value = courseData.level;
                    document.querySelector('select[name="sub_category"]').value = courseData
                        .sub_category;
                    document.getElementById('course-price').value = courseData.price;
                    document.getElementById('course-outcome').value = courseData.outcome;
                    document.getElementById('course-requirements').value = courseData.requirements;

                    const editCourseModal = new bootstrap.Modal(document.getElementById(
                        'editCourseModal'));
                    editCourseModal.show();
                });
            });
        });
    </script>
@endsection
