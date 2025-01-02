@extends('layout.layout')

@section('content')
  <style>
        #selected-instructors-box {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            min-height: 40px;
            padding: 5px;
            border: 1px solid #d1cbcb;
            background-color: #fff;
        }

        #selected-instructors-box>div {
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
                    <h1 class="app-page-title mb-5 ps-2">Create Course</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('courses.store') }}" method="POST"
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
                        @foreach ($errors->all() as $error)
                            <div class="text-danger text-center small pb-3">
                                {{ $error }}
                            </div>
                        @endforeach
                    @endif
                    <div class="row">
                        <div class="w-50 mb-3">
                            <input id="name" name="name" type="text" class="form-control signin-email"
                                placeholder="Course Name">
                            @if ($errors->has('name'))
                                <div class="text-danger small">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="w-50 mb-3">
                            <input id="language" name="language" type="text" class="form-control signin-email"
                                placeholder="Course Language">
                            @if ($errors->has('language'))
                                <div class="text-danger small">{{ $errors->first('language') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="description" name="description" class="textarea form-control" placeholder="Course Description"
                                rows="4" cols="50"></textarea>
                            @if ($errors->has('description'))
                                  <div id="error-message" class="text-danger small">{{ $errors->first('description') }}</div>
                            @endif
                              <div id="error-message" class="text-danger small"></div>
                        </div>
                    </div>
                    <script>
                    const descriptionInput = document.getElementById('description');
                    const errorMessage = document.getElementById('error-message');

                    descriptionInput.addEventListener('input', () => {
                    const maxLength = 100;
                    const currentLength = descriptionInput.value.length;

                        if (currentLength > maxLength) {
                            errorMessage.textContent = "You are not allowed to enter more than 100 characters.";
                            descriptionInput.value = descriptionInput.value.substring(0, maxLength);
                        } else {
                            errorMessage.textContent = "";
                        }
                    });
                    </script>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="level">
                                <option value="">Level of Course</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advance">Advance</option>
                            </select>
                            @if ($errors->has('level'))
                                <div class="text-danger small">{{ $errors->first('level') }}</div>
                            @endif
                        </div>
                        <div class="col-4 mb-3">
                            <input id="price" name="price" type="number" class="form-control signin-email"
                                placeholder="Course Price">
                            @if ($errors->has('price'))
                                <div class="text-danger small">{{ $errors->first('price') }}</div>
                            @endif
                        </div>
                        <div class="col-4 mb-3">
                            <input id="discount" name="discount" type="number" class="form-control signin-email"
                                placeholder="Discount">
                            @if ($errors->has('discount'))
                                <div class="text-danger small">{{ $errors->first('discount') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-100 mb-3">
                            <select class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                name="sub_category">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('sub_category'))
                                <div class="text-danger small">{{ $errors->first('sub_category') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="thumbnail" class="ps-2 pb-2">Upload Thumbnail Image</label>
                            <input id="thumbnail" name="thumbnail" type="file" class="form-control"
                                style="padding-top: 7px;" placeholder="Thumbnail Image">
                            @if ($errors->has('thumbnail'))
                                <div class="text-danger small">{{ $errors->first('thumbnail') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="w-50 mb-3">
                            <label for="demo_type" class="ps-2 pb-2">Choose your Demo Type</label>
                            <select id="demo-type"
                                class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control" name="demo_type"
                                onchange="toggleContentInput(this.value)">
                                <option value="file">Upload File</option>
                                <option value="url">Enter URL</option>
                            </select>
                        </div>
                        <div class="w-50 mb-3">
                            <div class="w-100 mb-3" id="file-input-container">
                                <label for="demo" class="ps-2 pb-2">Upload your Demo Video</label>
                                <input id="demo" name="demo" type="file" class="form-control signin-email"
                                    style="padding-block: 7px;" placeholder="Upload Lesson">
                                @if ($errors->has('demo'))
                                    <div class="text-danger small">{{ $errors->first('demo') }}</div>
                                @endif
                            </div>
                            <div class="w-100 mb-3 d-none" id="url-input-container">
                                <label for="url" class="ps-2 pb-2">Enter your Demo URL</label>
                                <input id="url" name="url" type="text" class="form-control signin-email"
                                    placeholder="Enter Demo URL">
                                @if ($errors->has('url'))
                                    <div class="text-danger small">{{ $errors->first('url') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="row h-auto">
                            @if (Auth::user()->role == 1 || Auth::user()->role == 3)
                            <div class="mb-3">
                                <div id="selected-instructors-container">
                                    <div id="selected-instructors-box" class="d-flex align-items-center px-2 bg-white">

                                    </div>
                                </div>

                                <div class="dropdown">
                                    <button
                                        class="btn btn-outline-white d-flex justify-content-between align-items-center dropdown-toggle w-100"
                                        type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Select Instructors
                                    </button>
                                    <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton"
                                        id="instructor-dropdown-menu">
                                        <div class="px-3 py-2">
                                            <input type="text" class="form-control" id="instructor-search"
                                                placeholder="Search Instructor" />
                                        </div>
                                        @foreach ($users as $user)
                                            <li><a class="dropdown-item" href="#"
                                                    data-instructor-id="{{ $user->id }}">{{ $user->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="overview" name="overview" class="textarea form-control" placeholder="Course Overview" rows="4"
                                cols="50"></textarea>
                            @if ($errors->has('overview'))
                                <div class="text-danger small">{{ $errors->first('overview') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="outcome" name="outcome" class="textarea form-control" placeholder="Course Outcome" rows="4"
                                cols="50"></textarea>
                            @if ($errors->has('outcome'))
                                <div class="text-danger small">{{ $errors->first('outcome') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row h-auto">
                        <div class="w-100 mb-3">
                            <textarea id="requirement" name="requirement" class="textarea form-control" placeholder="Course Requirement"
                                rows="4" cols="50"></textarea>
                            @if ($errors->has('requirement'))
                                <div class="text-danger small">{{ $errors->first('requirement') }}</div>
                            @endif
                        </div>
                    </div>


                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Create Course
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const instructorSearch = document.getElementById('instructor-search');
            const instructorDropdownMenu = document.getElementById('instructor-dropdown-menu');
            const selectedInstructorsBox = document.getElementById('selected-instructors-box');

            instructorSearch.addEventListener('input', () => {
                const searchQuery = instructorSearch.value.toLowerCase();
                const dropdownItems = instructorDropdownMenu.querySelectorAll('.dropdown-item');

                dropdownItems.forEach(item => {
                    const itemText = item.textContent.toLowerCase();
                    if (itemText.includes(searchQuery)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            instructorDropdownMenu.addEventListener('click', (e) => {
                const selectedItem = e.target.closest('.dropdown-item');
                if (selectedItem) {
                    const instructorId = selectedItem.getAttribute('data-instructor-id');
                    const instructorText = selectedItem.textContent;

                    const instructorElement = document.createElement('div');
                    instructorElement.classList.add('d-flex', 'm-0', 'align-items-center',
                        'instructor-name');
                    const instructorIndex = document.querySelectorAll('input[name^="instructors["]').length;
                    instructorElement.innerHTML = `
                <input type="hidden" name="instructors[${instructorIndex}]" value="${instructorId}">
                <div class="px-2 me-1 d-flex align-items-center" style="background-color: #ccc; border-radius: 14px;">
                    <p class="text-black me-1 mb-0" style="font-size: 14px;">${instructorText}</p>
                    <button type="button" class="btn border-0 p-0 remove-instructor-btn btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>
                </div>
            `;
                    selectedInstructorsBox.appendChild(instructorElement);

                    selectedItem.remove();
                }
            });

            selectedInstructorsBox.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-instructor-btn');
                if (removeBtn) {
                    const instructorElement = removeBtn.closest('.instructor-name');
                    const instructorId = instructorElement.querySelector('input').value;
                    const instructorText = instructorElement.querySelector('p').textContent;

                    const dropdownItem = document.createElement('li');
                    dropdownItem.innerHTML = `
                <a class="dropdown-item" href="#" data-instructor-id="${instructorId}">${instructorText}</a>
            `;
                    instructorDropdownMenu.appendChild(dropdownItem);

                    instructorElement.remove();
                }
            });

            function filterDropdown() {
                const selectedIds = Array.from(selectedInstructorsBox.querySelectorAll('input'))
                    .map(input => input.value);
                const dropdownItems = instructorDropdownMenu.querySelectorAll('.dropdown-item');

                dropdownItems.forEach(item => {
                    const instructorId = item.getAttribute('data-instructor-id');
                    if (selectedIds.includes(instructorId)) {
                        item.style.display = 'none';
                    } else {
                        item.style.display = 'block';
                    }
                });
            }
            filterDropdown();
        });
    </script>
    <script>
        function toggleContentInput(value) {
            if (value === 'file') {
                document.getElementById('file-input-container').classList.remove('d-none');
                document.getElementById('url-input-container').classList.add('d-none');
            } else if (value === 'url') {
                document.getElementById('file-input-container').classList.add('d-none');
                document.getElementById('url-input-container').classList.remove('d-none');
            } else {
                document.getElementById('file-input-container').classList.add('d-none');
                document.getElementById('url-input-container').classList.add('d-none');
            }
        }
    </script>
@endsection
