@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Sub Admins</h1>
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
                                    <form class="app-search-form" method="GET" action="{{ route('show.subadmin') }}">
                                        <input type="text" placeholder="Search..." name="search"
                                            class="form-control search-input" value="{{ request('search') }}">
                                        <button type="submit" class="btn search-btn" value="Search"><i
                                                class="fa-solid fa-magnifying-glass"></i></button>
                                    </form>
                                </div><!--//app-search-box-->

                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary" href="{{ route('download.subadmin', ['search'=> request('search')]) }}">
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
                                            <th class="cell text-center">Name</th>
                                            <th class="cell text-center">Email</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subadmins as $subadmin)
                                            <tr>
                                                <td class="cell text-center">{{ $subadmin->name }}</td>
                                                <td class="cell text-center">{{ $subadmin->email }}</td>
                                                <td class="cell d-flex justify-content-center">
                                                    <div>
                                                        <button type="button" class="btn app-btn-primary theme-btn mx-auto"
                                                            data-bs-toggle="modal" data-bs-target="#editSubAdminModal"
                                                            data-id="{{ $subadmin->id }}"
                                                            data-name="{{ $subadmin->name }}"
                                                            data-email="{{ $subadmin->email }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" class="bi bi-pen-fill"
                                                                viewBox="0 0 16 16">
                                                                <path
                                                                    d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="ps-3">
                                                        <form action="{{ route('destroy.subadmin', $subadmin->id) }}"
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
    <div class="modal fade" id="editSubAdminModal" tabindex="-1" aria-labelledby="editSubAdminModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubAdminModalLabel">Edit Sub Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('update.subadmin') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="subadmin-id">
                        <div class="mb-3">
                            <label for="subadmin-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="subadmin-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="subadmin-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="subadmin-email" name="email" required>
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
            var editSubAdminModal = document.getElementById('editSubAdminModal');
            editSubAdminModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');
                var email = button.getAttribute('data-email');

                var modalTitle = editSubAdminModal.querySelector('.modal-title');
                var modalBodyInputId = editSubAdminModal.querySelector('#subadmin-id');
                var modalBodyInputName = editSubAdminModal.querySelector('#subadmin-name');
                var modalBodyInputEmail = editSubAdminModal.querySelector('#subadmin-email');

                modalTitle.textContent = 'Edit Sub Admin';
                modalBodyInputId.value = id;
                modalBodyInputName.value = name;
                modalBodyInputEmail.value = email;
            });
        });
    </script>
@endsection
