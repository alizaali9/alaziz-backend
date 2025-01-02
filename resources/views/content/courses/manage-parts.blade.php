@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Manage Parts of Course ({{ $course->name }})</h1>
                    <a type="button" class="btn app-btn-primary theme-btn mx-auto mt-2" target="_blank"
                        href="{{ route('courses.createParts', ['courseid' => $course->id]) }}">
                        Add Course Parts
                    </a>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <div class="app-search-box">
                                    <form class="app-search-form" method="GET"
                                        action="{{ route('update.courses.parts', ['id' => $course->id]) }}">
                                        <input type="text" placeholder="Search..." name="search"
                                            class="form-control search-input" value="{{ request('search') }}">
                                        <button type="submit" class="btn search-btn" value="Search">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>
                                </div><!--//app-search-box-->
                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary"
                                    href="{{ route('parts.download.csv', ['courseid' => $course->id, 'search' => request()->get('search')]) }}">
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
                                            <th class="cell text-center">Course Part Name</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($parts as $part)
                                            <tr>
                                                <td class="cell text-center">{{ $part->name }}</td>
                                                <td class="cell d-flex justify-content-center">
                                                    <form
                                                        action="{{ route('course-parts.moveUp', ['id' => $part->id]) }}"
                                                        method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success rounded-4"
                                                            {{ $loop->first ? 'disabled' : '' }}>
                                                            <i class="fa-solid text-white fa-arrow-up"></i>
                                                        </button>
                                                    </form>

                                                    <form
                                                        action="{{ route('course-parts.moveDown', ['id' => $part->id]) }}"
                                                        method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-secondary  rounded-4"
                                                            {{ $loop->last ? 'disabled' : '' }}>
                                                            <i class="fa-solid text-white fa-arrow-down"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                        class="btn app-btn-primary theme-btn edit-part-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editPartModal"
                                                        data-id="{{ $part->id }}" data-name="{{ $part->name }}"
                                                        data-courseId="{{ $part->course_id }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-pen-fill"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                                        </svg>
                                                    </button>
                                                    <div class="ps-2">

                                                        <form
                                                            action="{{ route('courseParts.delete', ['id' => $part->id]) }}"
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

    <div class="modal fade" id="editPartModal" tabindex="-1" aria-labelledby="editPartModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPartModalLabel">Edit Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="edit-part-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="part-id">
                        <input type="hidden" name="course_id" id="course-id">
                        <div class="mb-3">
                            <label for="part-name" class="form-label">Part Name</label>
                            <input type="text" class="form-control" id="part-name" name="name" required>
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
            var editPartButtons = document.querySelectorAll('.edit-part-btn');
            var editPartForm = document.getElementById('edit-part-form');
            var partIdInput = document.getElementById('part-id');
            var courseIdInput = document.getElementById('course-id');
            var partNameInput = document.getElementById('part-name');

            editPartButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var id = this.getAttribute('data-id');
                    var name = this.getAttribute('data-name');
                    var course_id = this.getAttribute('data-courseId');

                    editPartForm.action = '{{ route('courseParts.update', ':id') }}'.replace(':id',
                        id);
                    partIdInput.value = id;
                    courseIdInput.value = course_id;
                    partNameInput.value = name;
                });
            });
        });
    </script>
@endsection
