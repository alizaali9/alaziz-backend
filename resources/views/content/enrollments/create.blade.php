@extends('layout.layout')

@section('content')
    <style>
        #selected-courses-box {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            min-height: 40px;
            padding: 5px;
            border: 1px solid #d1cbcb;
            background-color: #fff;
        }

        #selected-courses-box>div {
            margin-bottom: 5px;
        }

        .dropdown {
            background-color: #fff !important;
            border: 1px solid #ccc !important;
        }

        .dropdown:hover {
            background-color: #fff !important;
            border: 1px solid #ccc !important;
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
                    <h1 class="app-page-title mb-5 ps-2">Add Enrollments</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('store.enrollment') }}" method="POST"
                    enctype="multipart/form-data">
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
                        <div class="alert alert-danger">
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="w-50 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="student">
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->roll_no }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('student'))
                                <div class="text-danger small">{{ $errors->first('student') }}</div>
                            @endif
                        </div>
                        <!-- Course Selection with Search within the dropdown -->
                        <div class="w-50 mb-3">
                            <div id="selected-courses-container">
                                <div id="selected-courses-box" class="d-flex align-items-center px-2 bg-white"></div>
                            </div>

                            <!-- Dropdown with search input -->
                            <div class="dropdown">
                                <button class="btn btn-outline-white d-flex justify-content-between align-items-center dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Course
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton" id="course-dropdown-menu">
                                    <div class="px-3 py-2">
                                        <input type="text" class="form-control" id="course-search" placeholder="Search Course" />
                                    </div>
                                    @foreach ($courses as $course)
                                        <li><a class="dropdown-item" href="#" data-course-id="{{ $course->id }}">{{ $course->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            @if ($errors->has('courses'))
                                <div class="text-danger small">{{ $errors->first('courses') }}</div>
                            @endif
                        </div>

                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Add Enrollment
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const courseSearch = document.getElementById('course-search');
            const courseDropdownMenu = document.getElementById('course-dropdown-menu');
            const selectedCoursesBox = document.getElementById('selected-courses-box');

            // Filter dropdown items based on search input
            courseSearch.addEventListener('input', () => {
                const searchQuery = courseSearch.value.toLowerCase();
                const dropdownItems = courseDropdownMenu.querySelectorAll('.dropdown-item');

                dropdownItems.forEach(item => {
                    const itemText = item.textContent.toLowerCase();
                    if (itemText.includes(searchQuery)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            courseDropdownMenu.addEventListener('click', (e) => {
                const selectedItem = e.target.closest('.dropdown-item');
                if (selectedItem) {
                    const courseId = selectedItem.getAttribute('data-course-id');
                    const courseText = selectedItem.textContent;

                    const courseElement = document.createElement('div');
                    courseElement.classList.add('d-flex', 'm-0', 'align-items-center', 'course-name');
                    const courseIndex = document.querySelectorAll('input[name^="courses["]').length;
                    courseElement.innerHTML = `
                        <input type="hidden" name="courses[${courseIndex}]" value="${courseId}">
                        <div class="px-2 me-1 d-flex align-items-center" style="background-color: #ccc; border-radius: 14px;">
                            <p class="text-black me-1 mb-0" style="font-size: 14px;">${courseText}</p>
                            <button type="button" class="btn border-0 p-0 remove-course-btn btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    selectedCoursesBox.appendChild(courseElement);

                    selectedItem.remove();

                    // Close the dropdown after selection
                    const dropdownButton = document.getElementById('dropdownMenuButton');
                    const dropdown = new bootstrap.Dropdown(dropdownButton);
                    dropdown.hide();
                }
            });

            selectedCoursesBox.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-course-btn');
                if (removeBtn) {
                    const courseElement = removeBtn.closest('.course-name');
                    const courseId = courseElement.querySelector('input').value;
                    const courseText = courseElement.querySelector('p').textContent;

                    const option = document.createElement('li');
                    option.innerHTML = `<a class="dropdown-item" href="#" data-course-id="${courseId}">${courseText}</a>`;
                    courseDropdownMenu.appendChild(option);

                    courseElement.remove();
                }
            });
        });
    </script>
@endsection
