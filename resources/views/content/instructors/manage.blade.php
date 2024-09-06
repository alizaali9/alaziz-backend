@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Instructors</h1>
                    @if (session('success'))
                        <div class="text-success text-center small pt-2">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <div class="app-search-box">
                                    <form class="app-search-form" method="GET" action="{{ route('show.instructor') }}">
                                        <input type="text" placeholder="Search..." name="search"
                                            class="form-control search-input" value="{{ request('search') }}">
                                        <button type="submit" class="btn search-btn" value="Search"><i
                                                class="fa-solid fa-magnifying-glass"></i></button>
                                    </form>
                                </div><!--//app-search-box-->

                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary"
                                    href="{{ route('download.instructor', ['search' => request('search')]) }}">
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

            <div class="tab-content" id="orders-table-tab-content">
                <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
                    <div class="app-card app-card-orders-table shadow-sm mb-5">
                        <div class="app-card-body">
                            <div class="table-responsive">
                                <table class="table app-table-hover mb-0 text-left">
                                    <thead>
                                        <tr>
                                            <th class="cell text-center">Picture</th>
                                            <th class="cell text-center">Name</th>
                                            <th class="cell text-center">Email</th>
                                            <th class="cell text-center">About</th>
                                            <th class="cell text-center">Skills</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($instructors as $instructor)
                                            <tr>
                                                <td class="cell text-center">
                                                    @if ($instructor->picture)
                                                        <img src="{{ asset('storage/' . $instructor->picture) }}"
                                                            alt="{{ $instructor->name }}'s Picture" class="img-thumbnail"
                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('assets/images/user.png') }}"
                                                            alt="{{ $instructor->name }}'s Picture" class="img-thumbnail"
                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                </td>
                                                <td class="cell text-center">{{ $instructor->name }}</td>
                                                <td class="cell text-center">{{ $instructor->user->email }}</td>
                                                <td class="cell text-center">{{ $instructor->about }}</td>
                                                <td class="cell text-center">{{ $instructor->skills }}</td>
                                                <td class="cell text-center">
                                                    <div>
                                                        <button type="button" class="btn app-btn-primary theme-btn mx-auto"
                                                            data-bs-toggle="modal" data-bs-target="#editInstructorModal"
                                                            data-id="{{ $instructor->id }}"
                                                            data-name="{{ $instructor->name }}"
                                                            data-email="{{ $instructor->user->email }}"
                                                            data-about="{{ $instructor->about }}"
                                                            data-skills="{{ $instructor->skills }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" class="bi bi-pen-fill"
                                                                viewBox="0 0 16 16">
                                                                <path
                                                                    d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="ps-3">
                                                        <form action="{{ route('destroy.instructor', $instructor->id) }}"
                                                            method="post">
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
    </div>

    <!-- Edit Instructor Modal -->
    <div class="modal fade" id="editInstructorModal" tabindex="-1" aria-labelledby="editInstructorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInstructorModalLabel">Edit Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('update.instructor') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="instructor-id">
                        <div class="mb-3">
                            <label for="instructor-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="instructor-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="instructor-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="instructor-email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="instructor-about" class="form-label">About</label>
                            <textarea class="form-control" id="instructor-about" name="about" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="instructor-skills" class="form-label">Skills</label>
                            <input type="text" class="form-control" id="instructor-skills" name="skills" required>
                        </div>
                        <div class="mb-3">
                            <label for="instructor-picture" class="form-label">Picture</label>
                            <input type="file" class="form-control" id="instructor-picture" name="picture">
                            <small class="text-muted">Leave blank to keep current picture.</small>
                            <div id="current-picture" class="mt-2"></div>
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
            var editInstructorModal = document.getElementById('editInstructorModal');
            editInstructorModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');
                var email = button.getAttribute('data-email');
                var about = button.getAttribute('data-about');
                var skills = button.getAttribute('data-skills');
                var picture = button.getAttribute('data-picture');

                var modalTitle = editInstructorModal.querySelector('.modal-title');
                var modalBodyInputId = editInstructorModal.querySelector('#instructor-id');
                var modalBodyInputName = editInstructorModal.querySelector('#instructor-name');
                var modalBodyInputEmail = editInstructorModal.querySelector('#instructor-email');
                var modalBodyTextareaAbout = editInstructorModal.querySelector('#instructor-about');
                var modalBodyInputSkills = editInstructorModal.querySelector('#instructor-skills');
                var modalBodyCurrentPicture = editInstructorModal.querySelector('#current-picture');

                modalTitle.textContent = 'Edit Instructor';
                modalBodyInputId.value = id;
                modalBodyInputName.value = name;
                modalBodyInputEmail.value = email;
                modalBodyTextareaAbout.value = about;
                modalBodyInputSkills.value = skills;

                if (picture) {
                    modalBodyCurrentPicture.innerHTML = `
                        <img src="{{ asset('storage/') }}/${picture}" alt="Instructor Picture" width="100">
                    `;
                } else {
                    modalBodyCurrentPicture.innerHTML = '';
                }
            });
        });
    </script>
@endsection
