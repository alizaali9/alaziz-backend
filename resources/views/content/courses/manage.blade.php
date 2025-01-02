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
                                    <form class="app-search-form" method="GET" action="{{ route('courses.show') }}">
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
                                    href="{{ route('courses.download.csv', ['search' => request()->get('search')]) }}">
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
                                                            data-course="{{ htmlspecialchars(
                                                                json_encode([
                                                                    'id' => $course->id,
                                                                    'name' => addslashes($course->name),
                                                                    'creators' => addslashes($course->creators),
                                                                    'language' => addslashes($course->language),
                                                                    'overview' => addslashes($course->overview),
                                                                    'description' => addslashes($course->description),
                                                                    'level' => $course->level,
                                                                    'sub_category' => $course->subcategory->id,
                                                                    'price' => $course->price,
                                                                    'discount' => $course->discount,
                                                                    'outcome' => addslashes($course->outcome),
                                                                    'requirements' => addslashes($course->requirements),
                                                                    'demo' => $course->demo_video,
                                                                ]),
                                                                ENT_QUOTES,
                                                                'UTF-8',
                                                            ) }}">
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
                                                        <form action="{{ route('courses.delete', ['id' => $course->id]) }}"
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
                            <textarea class="form-control" id="course-description" name="description" rows="4" value='' required></textarea>
                            <div id="error-message" class="text-danger small"></div>
                            <script>
                                const descriptionInput = document.getElementById('course-description');
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
                            <label for="course-discount" class="form-label">Discount</label>
                            <input type="number" class="form-control" id="course-discount" name="discount" required>
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
                        <div class="row justify-content-center flex-wrap">
                            <div class="w-100 mb-3">
                                <label for="demo" class="ps-2 pb-2">Choose your Demo Type</label>
                                <select id="content-type"
                                    class="form-select form-select-sm ms-auto d-inline-flex w-100 form-control"
                                    name="demo_type" onchange="toggleContentInput(this.value)">
                                    <option value="file">Upload File</option>
                                    <option value="url">Enter URL</option>
                                </select>
                            </div>
                            <div class="w-100 mb-3">
                                <div class="w-100 mb-3" id="file-input-container">
                                    <label for="demo" class="ps-2 pb-2">Upload your Demo Video</label>
                                    <input id="demo" name="demo" type="file"
                                        class="form-control signin-email" style="padding-block: 7px;"
                                        placeholder="Upload Lesson">
                                    @if ($errors->has('demo'))
                                        <div class="text-danger small">{{ $errors->first('demo') }}</div>
                                    @endif
                                </div>
                                <div class="w-100 mb-3 d-none" id="url-input-container">
                                    <label for="url" class="ps-2 pb-2">Enter your Demo URL</label>
                                    <input id="url" name="url" type="text"
                                        class="form-control signin-email" placeholder="Enter Demo URL">
                                    @if ($errors->has('url'))
                                        <div class="text-danger small">{{ $errors->first('url') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if (Auth::user()->role == 1 || Auth::user()->role == 3)
                            <div class="mb-3">
                                <div id="selected-instructors-container">
                                    <div id="selected-instructors-box" class="d-flex align-items-center px-2 bg-white">
                                        @foreach ($course->creators as $creator)
                                            <div class="d-flex m-0 align-items-center instructor-name">
                                                <input type="hidden" name="instructors[]" value="{{ $creator->id }}">
                                                <div class="px-2 me-1 d-flex align-items-center"
                                                    style="background-color: #ccc; border-radius: 14px;">
                                                    <p class="text-black me-1 mb-0" style="font-size: 14px;">
                                                        {{ $creator->name }}</p>
                                                    <button type="button"
                                                        class="btn border-0 p-0 remove-instructor-btn btn-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="#000000" class="bi bi-x"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function decodeHTML(html) {
                const txt = document.createElement('textarea');
                txt.innerHTML = html;
                return txt.value;
            }

            document.querySelectorAll('.edit-course-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const course = this.dataset.course;
                    const decodedCourse = course.replace(/&quot;/g, '"').replace(/&#39;/g, "'");
                    // console.log(decodedCourse);
                    const courseData = JSON.parse(decodedCourse);
                    console.log(courseData.creators);

                    const isValidUrl = (url) => {
                        try {
                            new URL(url);
                            return true;
                        } catch (_) {
                            return false;
                        }
                    };

                    console.log(courseData.demo);
                    if (isValidUrl(courseData.demo)) {
                        document.getElementById('file-input-container').classList.add('d-none');
                        document.getElementById('url-input-container').classList.remove('d-none');
                        document.querySelector('select[name="demo_type"]').value = 'url';

                        document.getElementById('url').value = courseData.demo;
                    }

                    const updateUrl = `{{ url('courses/update') }}/${courseData.id}`;
                    document.getElementById('edit-course-form').action = updateUrl;

                    document.getElementById('course-id').value = courseData.id;
                    document.getElementById('course-name').value = decodeHTML(courseData.name);
                    document.getElementById('course-language').value = decodeHTML(courseData
                        .language);
                    document.getElementById('course-overview').value = decodeHTML(courseData
                        .overview);
                    document.getElementById('course-description').value = decodeHTML(courseData
                        .description);
                    document.querySelector('select[name="level"]').value = courseData.level;
                    document.querySelector('select[name="sub_category"]').value = courseData
                        .sub_category;
                    // document.querySelector('select[name="instructor"]').value = courseData
                    //     .created_by;
                    document.getElementById('course-price').value = courseData.price;
                    document.getElementById('course-discount').value = courseData.discount;
                    document.getElementById('course-outcome').value = decodeHTML(courseData
                        .outcome);
                    document.getElementById('course-requirements').value = decodeHTML(courseData
                        .requirements);

                    const editCourseModal = new bootstrap.Modal(document.getElementById(
                        'editCourseModal'));
                    editCourseModal.show();
                });
            });
        });
    </script>
@endsection
