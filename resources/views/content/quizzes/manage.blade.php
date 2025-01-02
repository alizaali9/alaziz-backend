@extends('layout.layout')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Quizzes</h1>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <div class="app-search-box">
                                    <form class="app-search-form" method="GET" action="{{ route('manage.quiz') }}">
                                        <input type="text" placeholder="Search..." name="search"
                                            class="form-control search-input" value="{{ request('search') }}">
                                        <button type="submit" class="btn search-btn" value="Search">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>
                                </div><!--//app-search-box-->
                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary" href="{{ route('download.quizzes') }}">
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

            <!-- Display all validation errors here -->
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="text-danger text-center small pb-1">
                        {{ $error }}
                    </div>
                @endforeach
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
                                            <th class="cell text-center">Quiz Name</th>
                                            <th class="cell text-center">Quiz Category</th>
                                            <th class="cell text-center">Quiz Duration</th>
                                            <th class="cell text-center">Quiz Price</th>
                                            <th class="cell text-center">Quiz Discount</th>
                                            <th class="cell text-center">No. of tries</th>
                                            <th class="cell text-center">Quiz Status</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quizzes as $quiz)
                                            <tr>
                                                <td class="cell text-center">{{ $quiz->name }}</td>
                                                <td class="cell text-center">{{ $quiz->subcategory->name }}</td>
                                                <td class="cell text-center">{{ $quiz->timelimit }}</td>
                                                <td class="cell text-center">{{ $quiz->price }}</td>
                                                <td class="cell text-center">{{ $quiz->discount }}</td>
                                                <td class="cell text-center">{{ $quiz->tries }}</td>
                                                <td class="cell text-center">
                                                    <label class="switch">
                                                        <input type="checkbox" class="status-toggle"
                                                            data-id="{{ $quiz->id }}"
                                                            {{ $quiz->status == 1 ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </td>
                                                <td class="cell d-flex justify-content-center">
                                                    <div>
                                                        <button type="button"
                                                            class="btn app-btn-primary theme-btn mx-auto edit-quiz-btn"
                                                            data-bs-toggle="modal" data-bs-target="#editQuizModal"
                                                            data-id="{{ $quiz->id }}" data-name="{{ $quiz->name }}"
                                                            data-sub_category="{{ $quiz->sub_category }}"
                                                            data-timelimit="{{ $quiz->timelimit }}"
                                                            data-price="{{ $quiz->price }}"
                                                            data-discount="{{ $quiz->discount }}"
                                                            data-tries="{{ $quiz->tries }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" class="bi bi-pen-fill"
                                                                viewBox="0 0 16 16">
                                                                <path
                                                                    d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                                            </svg>
                                                        </button>

                                                    </div>
                                                    <div class="ps-3">
                                                        <form action="{{ route('quizzes.destroy', ['id' => $quiz->id]) }}"
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
    <!-- Modal -->
    <div class="modal fade" id="editQuizModal" tabindex="-1" aria-labelledby="editQuizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editQuizForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editQuizModalLabel">Edit Quiz</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quizName" class="form-label text-primary fw-bold">Quiz Name</label>
                            <input type="text" class="form-control" id="quizName" name="name">
                            <p class="text-muted small">The Quiz Name should not be greater than 18 characters.</p>
                        </div>
                        <div class="mb-3">
                            <label for="quizCategory" class="form-label text-primary fw-bold">Quiz Category</label>
                            <select class="form-control form-select py-1" id="quizCategory" name="sub_category">
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quizTimelimit" class="form-label text-primary fw-bold">Time Limit</label>
                            <input type="text" class="form-control" id="quizTimelimit" name="timelimit">
                        </div>
                        <div class="mb-3">
                            <label for="quizTries" class="form-label text-primary fw-bold">Number of Tries</label>
                            <input type="number" class="form-control" id="quizTries" name="tries" min="1">
                        </div>
                        <div class="mb-3">
                            <label for="quizPrice" class="form-label text-primary fw-bold">Price</label>
                            <input type="number" class="form-control" id="quizPrice" name="price" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="quizDiscount" class="form-label text-primary fw-bold">Discount</label>
                            <input type="number" class="form-control" id="quizDiscount" name="discount" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="quizThumbnail" class="form-label text-primary fw-bold">Thumbnail</label>
                            <input type="file" class="form-control py-2" id="quizThumbnail" name="thumbnail"
                                accept=".png, .jpg, .jpeg">
                                <p class="text-muted small">Only PNG, JPG, JPEG format supported.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn app-btn-secondary theme-btn"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn app-btn-primary theme-btn">Update Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successmsg = document.getElementById('success-msg');

            document.querySelectorAll('.status-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const quizId = this.getAttribute('data-id');
                    console.log(quizId);
                    const status = this.checked ? 1 : 0;

                    fetch(`/quizzes/${quizId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                status
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                successmsg.innerText = 'Status Updated Successfully';
                            }
                        });
                });
            });

            // Handle edit button click
            document.querySelectorAll('.edit-quiz-btn').forEach(button => {
                button.addEventListener('click', function() {
                    successmsg.innerText = "";

                    const quizId = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const sub_category = this.getAttribute('data-sub_category');
                    const timelimit = this.getAttribute('data-timelimit');
                    const price = this.getAttribute('data-price');
                    const discount = this.getAttribute('data-discount');
                    const tries = this.getAttribute('data-tries');

                    const form = document.getElementById('editQuizForm');
                    const updateUrl = `{{ url('quizzes/update') }}/${quizId}`;
                    form.action = updateUrl;

                    document.getElementById('quizName').value = name;
                    document.getElementById('quizCategory').value = sub_category;
                    document.getElementById('quizTimelimit').value = timelimit;
                    document.getElementById('quizPrice').value = price;
                    document.getElementById('quizDiscount').value = discount;
                    document.getElementById('quizTries').value = tries;
                });
            });
        });
    </script>
@endsection
