@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Students Registered</h1>
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
                                    <form class="app-search-form" method="GET" action="{{ route('students') }}">
                                        <input type="text" placeholder="Search..." name="search"
                                            class="form-control search-input" value="{{ request('search') }}">
                                        <button type="submit" class="btn search-btn" value="Search"><i
                                                class="fa-solid fa-magnifying-glass"></i></button>
                                    </form>
                                </div><!--//app-search-box-->
                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary"
                                    href="{{ route('students.downloadCSV', ['search' => request('search')]) }}">
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
                                            <th class="cell text-center">Roll No</th>
                                            <th class="cell text-center">Email</th>
                                            <th class="cell text-center">Whatsapp Number</th>
                                            <th class="cell text-center">City</th>
                                            <th class="cell text-center">Country</th>
                                            <th class="cell text-center">IMMI Number</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student)
                                            <tr>
                                                <td class="cell text-center">
                                                    @if ($student->picture)
                                                        <img src="{{ asset('storage/' . $student->picture) }}"
                                                            alt="{{ $student->name }}'s Picture" class="img-thumbnail"
                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('assets/images/user.png') }}"
                                                            alt="{{ $student->name }}'s Picture" class="img-thumbnail"
                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                </td>
                                                <td class="cell text-center">{{ $student->name }}</td>
                                                <td class="cell text-center">{{ $student->roll_no }}</td>
                                                <td class="cell text-center">{{ $student->email }}</td>
                                                <td class="cell text-center">{{ $student->whatsapp_no }}</td>
                                                <td class="cell text-center">{{ $student->city }}</td>
                                                <td class="cell text-center">{{ $student->country }}</td>
                                                <td class="cell text-center">{{ $student->immi_number }}</td>
                                                <td class="cell text-center">
                                                    <div class="ps-3">
                                                        <form action="{{ route('delete.student.admin', $student->id) }}"
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
                            </div>
                        </div><!--//app-card-body-->
                    </div><!--//app-card-->
                </div><!--//tab-pane-->
            </div><!--//tab-content-->
        </div><!--//container-fluid-->
    </div>
@endsection
