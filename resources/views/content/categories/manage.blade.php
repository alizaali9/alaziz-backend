@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Course Categories</h1>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <div class="app-search-box">
                                    <form class="app-search-form" action="{{ route('show.categories') }}" method="GET">
                                        <input type="text" placeholder="Search..." name="search" class="form-control search-input"
                                            value="{{ request('search') }}">
                                        <button type="submit" class="btn search-btn" value="Search">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>
                                </div><!--//app-search-box-->
                            </div><!--//col-->
                            <div class="col-auto">
                                <a class="btn app-btn-secondary" href="{{ route('download.csv', ['search' => request()->get('search')]) }}">
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
                @foreach ($errors->all() as $error)
                    <div class="text-danger text-center small pb-3">
                        {{ $error }}
                    </div>
                @endforeach
            @endif

            <div class="tab-content" id="orders-table-tab-content">
                <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
                    <div class="app-card app-card-orders-table shadow-sm mb-5">
                        <div class="app-card-body">
                            <div class="table-responsive">
                                <table class="table app-table-hover mb-0 text-left">
                                    <thead>
                                        <tr>
                                            <th class="cell text-center">Category No</th>
                                            <th class="cell text-center">Category Name</th>
                                            <th class="cell text-center">No.of Courses</th>
                                            <th class="cell text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td class="cell text-center">{{ $category->id }}</td>
                                                <td class="cell text-center">{{ $category->name }}</td>
                                                <td class="cell text-center">{{ $category->courses_count }}</td>
                                                <td class="cell d-flex justify-content-center">
                                                    <div>
                                                        <button type="button"
                                                            class="btn app-btn-success theme-btn mx-auto toggle-subcategories"
                                                            data-id="{{ $category->id }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor"
                                                                class="bi bi-arrow-down-circle-fill" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="ps-3">

                                                        <button type="button"
                                                            class="btn app-btn-primary theme-btn mx-auto edit-category-btn"
                                                            data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                            data-subcategories="{{ json_encode($category->subcategories) }}"
                                                            data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" class="bi bi-pen-fill"
                                                                viewBox="0 0 16 16">
                                                                <path
                                                                    d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                                            </svg>
                                                        </button>

                                                    </div>
                                                    <div class="ps-3">
                                                        <form
                                                            action="{{ route('delete.category', ['id' => $category->id]) }}"
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
                                            <tr class="subcategories w-100" id="subcategories-{{ $category->id }}"
                                                style="display:none;">
                                                <td colspan="4" class="w-100">
                                                    <ul class="mb-0">
                                                        @foreach ($category->subcategories as $subcategory)
                                                            <li class="subcategory-item">
                                                                {{ $subcategory->name }}
                                                                ({{ $subcategory->courses->count() }} courses)
                                                            </li>
                                                        @endforeach
                                                    </ul>
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
    <!-- Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label text-primary fw-bold">Category</label>
                            <input type="text" class="form-control" id="categoryName" name="name">
                        </div>
                        <div id="subcategoryList">
                            <label for="subcategories" class="form-label text-primary fw-bold">Sub Categories</label>
                            <ul class="list-group" id="subcategoryInputs">
                            </ul>
                            <button type="button" class="btn app-btn-primary theme-btn mt-2" id="addSubcategoryButton">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn app-btn-secondary theme-btn"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn app-btn-primary theme-btn">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-subcategories').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    console.log(id);
                    const subcategoryRow = document.getElementById(`subcategories-${id}`);
                    console.log(subcategoryRow);
                    if (subcategoryRow.style.display === 'none') {
                        subcategoryRow.style.display = 'table-row';
                    } else {
                        subcategoryRow.style.display = 'none';
                    }
                });
            });
            document.querySelectorAll('.edit-category-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-id');
                    const categoryName = this.getAttribute('data-name');
                    const subcategories = JSON.parse(this.getAttribute('data-subcategories'));


                    const form = document.getElementById('editCategoryForm');
                    form.action =
                        `/update-category/${categoryId}`;

                    document.getElementById('categoryName').value = categoryName;

                    const subcategoryList = document.getElementById('subcategoryInputs');
                    subcategoryList.innerHTML = '';

                    subcategories.forEach(subcategory => {
                        const listItem = document.createElement('li');
                        listItem.className = 'list-group-item d-flex';
                        listItem.innerHTML = `<input type="text" class="form-control subcats" value="${subcategory.name}" name="subcategories[${subcategory.id}][name]" data-id="${subcategory.id}">
                                        <input type="hidden" name="subcategories[${subcategory.id}][id]" value="${subcategory.id}">
                                      <button type="button" class="btn btn-danger btn-sm ms-2 remove-subcategory">
                                          <i class="fas fa-minus text-white"></i>
                                      </button>`;
                        subcategoryList.appendChild(listItem);
                    });

                    addRemoveSubcategoryEvent();
                });
            });

            document.getElementById('addSubcategoryButton').addEventListener('click', function() {
                const subcategoryList = document.getElementById('subcategoryInputs');
                const subcats = document.querySelectorAll('.subcats');
                // console.log("List Item",listItem);
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex';
                listItem.innerHTML = `<input type="text" class="form-control subcats" placeholder="New Sub Category" name="subcategories[${subcats.length + 1}][name]" data-id="${subcats.length + 1}">
                                <input type="hidden" name="subcategories[${subcats.length + 1}][id]" value="${subcats.length + 1}">
                              <button type="button" class="btn btn-danger btn-sm ms-2 remove-subcategory">
                                  <i class="fas fa-minus text-white"></i>
                              </button>`;
                subcategoryList.appendChild(listItem);

                addRemoveSubcategoryEvent();
            });

            function addRemoveSubcategoryEvent() {
                document.querySelectorAll('.remove-subcategory').forEach(button => {
                    button.addEventListener('click', function() {
                        this.parentElement.remove();
                    });
                });
            }

            addRemoveSubcategoryEvent();
        });
    </script>
@endsection
