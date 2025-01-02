@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Manage Social Links</h1>
                </div>
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
            {{-- <div class="col-auto mb-3 ">
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
                    </div><!--//row-->
                </div><!--//table-utilities-->
            </div><!--//col-auto--> --}}

            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">

                    <div class="table-responsive">
                        <table class="table app-table-hover mb-0 text-left">
                            <thead>
                                <tr>
                                    <th class="cell text-center">Social Link Type</th>
                                    <th class="cell text-center">Social Link</th>
                                    <th class="cell text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($links as $link)
                                    <tr>
                                        <td class="cell text-center">{{ ucfirst($link->social_account) }}</td>
                                        <td class="cell text-center">
                                            <a target="_blank" href="{{ $link->social_link }}">
                                                {{ $link->social_link }}
                                            </a>
                                        </td>
                                        <td class="cell text-center">
                                            <div class="d-inline-flex">
                                                <!-- Edit Button -->
                                                <button class="btn app-btn-secondary me-2" data-bs-toggle="modal"
                                                    data-bs-target="#editLinkModal{{ $link->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                                        <path
                                                            d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                                    </svg>
                                                </button>
                                                <!-- Delete Button -->
                                                <form action="{{ route('social.links.delete', $link->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn app-btn-danger theme-btn mx-auto">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-trash-fill"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editLinkModal{{ $link->id }}" tabindex="-1"
                                        aria-labelledby="editLinkModalLabel{{ $link->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('social.links.update', $link->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editLinkModalLabel{{ $link->id }}">
                                                            Edit Social Link
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="social_account{{ $link->id }}"
                                                                class="form-label">Social Account</label>
                                                            <select id="social_account{{ $link->id }}"
                                                                name="social_account" class="form-select" required>
                                                                <option value="email"
                                                                    {{ $link->social_account == 'email' ? 'selected' : '' }}>
                                                                    Email</option>
                                                                <option value="facebook"
                                                                    {{ $link->social_account == 'facebook' ? 'selected' : '' }}>
                                                                    Facebook</option>
                                                                <option value="instagram"
                                                                    {{ $link->social_account == 'instagram' ? 'selected' : '' }}>
                                                                    Instagram</option>
                                                                <option value="twitter"
                                                                    {{ $link->social_account == 'twitter' ? 'selected' : '' }}>
                                                                    Twitter</option>
                                                                <option value="linkedin"
                                                                    {{ $link->social_account == 'linkedin' ? 'selected' : '' }}>
                                                                    LinkedIn</option>
                                                                <option value="whatsapp"
                                                                    {{ $link->social_account == 'whatsapp' ? 'selected' : '' }}>
                                                                    WhatsApp</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="social_link{{ $link->id }}"
                                                                class="form-label">Social Link</label>
                                                            <input type="text" id="social_link{{ $link->id }}"
                                                                name="social_link" class="form-control"
                                                                value="{{ $link->social_link }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn app-btn-secondary theme-btn"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn app-btn-primary theme-btn">Save
                                                            changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No Social Links Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div><!--//table-responsive-->
                </div><!--//app-card-body-->
            </div><!--//app-card-->
        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
