@extends('layout.layout')

@section('content')
    <style>
        #selected-quizzes-box {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            min-height: 40px;
            padding: 5px;
            border: 1px solid #d1cbcb;
            background-color: #fff;
        }

        #selected-quizzes-box > div {
            margin-bottom: 5px;
        }

        .dropdown{
            background-color: #fff!important;
            border: 1px solid #ccc!important;
        }
        .dropdown:hover{
            background-color: #fff!important;
            border: 1px solid #ccc!important;
        }

        .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }

        .dropdown-item {
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f0f0f0;
        }
    </style>

    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Add Quiz Enrollments</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('store.quiz.enrollment') }}" method="POST" enctype="multipart/form-data">
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
                        @foreach ($errors->all() as $error)
                            <div class="text-danger text-center small pb-3">
                                {{ $error }}
                            </div>
                        @endforeach
                    @endif

                    <div class="row">
                        <!-- Student Selection -->
                        <div class="w-50 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control" name="student">
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->roll_no }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('student'))
                                <div class="text-danger small">{{ $errors->first('student') }}</div>
                            @endif
                        </div>

                        <!-- Quiz Selection with Search within the dropdown -->
                        <div class="w-50 mb-3">
                            <div id="selected-quizzes-container">
                                <div id="selected-quizzes-box" class="d-flex align-items-center px-2 bg-white"></div>
                            </div>

                            <!-- Dropdown with search input -->
                            <div class="dropdown">
                                <button class="btn btn-outline-white d-flex justify-content-between align-items-center dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Quiz
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton" id="quiz-dropdown-menu">
                                    <div class="px-3 py-2">
                                        <input type="text" class="form-control" id="quiz-search" placeholder="Search Quiz" />
                                    </div>
                                    @foreach ($quizzes as $quiz)
                                        <li><a class="dropdown-item" href="#" data-quiz-id="{{ $quiz->id }}">{{ $quiz->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            @if ($errors->has('quizzes'))
                                <div class="text-danger small">{{ $errors->first('quizzes') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Add Enrollment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const quizSearch = document.getElementById('quiz-search');
            const quizDropdownMenu = document.getElementById('quiz-dropdown-menu');
            const selectedQuizzesBox = document.getElementById('selected-quizzes-box');

            // Filter dropdown items based on search input
            quizSearch.addEventListener('input', () => {
                const searchQuery = quizSearch.value.toLowerCase();
                const dropdownItems = quizDropdownMenu.querySelectorAll('.dropdown-item');

                dropdownItems.forEach(item => {
                    const itemText = item.textContent.toLowerCase();
                    if (itemText.includes(searchQuery)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Handle quiz selection and add it to the selected list
            quizDropdownMenu.addEventListener('click', (e) => {
                const selectedItem = e.target.closest('.dropdown-item');
                if (selectedItem) {
                    const quizId = selectedItem.getAttribute('data-quiz-id');
                    const quizText = selectedItem.textContent;

                    // Add the selected quiz to the selected quizzes box
                    const quizElement = document.createElement('div');
                    quizElement.classList.add('d-flex', 'm-0', 'align-items-center', 'quiz-name');
                    const quizIndex = document.querySelectorAll('input[name^="quizzes["]').length;
                    quizElement.innerHTML = `
                        <input type="hidden" name="quizzes[${quizIndex}]" value="${quizId}">
                        <div class="px-2 me-1 d-flex align-items-center" style="background-color: #ccc; border-radius: 14px;">
                            <p class="text-black me-1 mb-0" style="font-size: 14px;">${quizText}</p>
                            <button type="button" class="btn border-0 p-0 remove-quiz-btn btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    selectedQuizzesBox.appendChild(quizElement);

                    // Remove selected quiz from the dropdown
                    selectedItem.remove();

                    // Close the dropdown after selection
                    const dropdownButton = document.getElementById('dropdownMenuButton');
                    const dropdown = new bootstrap.Dropdown(dropdownButton);
                    dropdown.hide();
                }
            });

            // Handle removing a quiz from the selected list
            selectedQuizzesBox.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-quiz-btn');
                if (removeBtn) {
                    const quizElement = removeBtn.closest('.quiz-name');
                    const quizId = quizElement.querySelector('input').value;
                    const quizText = quizElement.querySelector('p').textContent;

                    const option = document.createElement('li');
                    option.innerHTML = `<a class="dropdown-item" href="#" data-quiz-id="${quizId}">${quizText}</a>`;
                    quizDropdownMenu.appendChild(option);

                    quizElement.remove();
                }
            });
        });
    </script>
@endsection
