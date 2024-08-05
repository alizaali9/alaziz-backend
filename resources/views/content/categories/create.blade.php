@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl w-50 justify-content-center d-flex align-items-center" style="height: 70vh;">
            <div class="auth-form-container text-start w-100">
                <form class="auth-form login-form" action="{{ route('post.category') }}" method="POST">
                    @csrf
                    @if (session('success'))
                        <div class="text-success text-center small pb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="text-success text-center small pb-3">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="email mb-3">
                        <label class="pb-2 ps-2" for="name">Category Name</label>
                        <input id="name" name="name" type="text" class="form-control signin-email"
                            placeholder="Category">
                        @if ($errors->has('category'))
                            <div class="text-danger small">{{ $errors->first('category') }}</div>
                        @endif
                    </div>
                    <div id="input-containers">
                        <div class="row justify-content-between input-container">
                            <div class="mb-3" style="width: 87%">
                                <input id="subcategory-0" name="subcategory[0]" type="text"
                                    class="form-control signin-email" placeholder="Course Sub Category">
                                @if ($errors->has('subcategory'))
                                    <div class="text-danger small">{{ $errors->first('subcategory') }}</div>
                                @endif
                            </div>
                            <div style="width: 13%">
                                <button type="button" class="btn app-btn-primary theme-btn mx-auto"
                                    style="padding-inline: 14px;" onclick="cloneContainer()">+
                                </button>
                            </div>
                            <div style="width: 13%; display: none;">
                                <button type="button" class="btn app-btn-danger theme-btn mx-auto"
                                    onclick="removeContainer(this)">-
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Category
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script>
        let containerIndex = 1;

        function cloneContainer() {
            const containers = document.querySelectorAll('.input-container');

            if (containers.length > 0) {
                const lastContainer = containers[containers.length - 1];
                lastContainer.children[1].style.display = 'none';
                lastContainer.children[2].style.display = 'block';
            }

            // Create a new container
            const newContainer = document.createElement('div');
            newContainer.className = 'row justify-content-center input-container';

            const inputDiv = document.createElement('div');
            inputDiv.className = 'mb-3';
            inputDiv.style.width = '87%';
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `subcategory[${containerIndex}]`;
            input.id = `subcategory-${containerIndex}`;
            input.className = 'form-control signin-email';
            input.placeholder = 'Course Sub Category';
            inputDiv.appendChild(input);

            const addButtonDiv = document.createElement('div');
            addButtonDiv.style.width = '13%';
            const addButton = document.createElement('button');
            addButton.type = 'button';
            addButton.className = 'btn app-btn-primary theme-btn mx-auto';
            addButton.style.paddingInline = "14px";
            addButton.onclick = cloneContainer;
            addButton.textContent = '+';
            addButtonDiv.appendChild(addButton);

            const removeButtonDiv = document.createElement('div');
            removeButtonDiv.style.width = '13%';
            removeButtonDiv.style.display = 'none';
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn app-btn-danger theme-btn mx-auto';
            removeButton.onclick = function() {
                removeContainer(this);
            };
            removeButton.textContent = '-';
            removeButtonDiv.appendChild(removeButton);

            newContainer.appendChild(inputDiv);
            newContainer.appendChild(addButtonDiv);
            newContainer.appendChild(removeButtonDiv);

            document.getElementById('input-containers').appendChild(newContainer);

            containerIndex++;
        }

        function removeContainer(button) {
            const container = button.closest('.input-container');
            container.remove();

            const containers = document.querySelectorAll('.input-container');
            if (containers.length > 0) {
                const lastContainer = containers[containers.length - 1];
                lastContainer.children[1].style.display = 'block';
                lastContainer.children[2].style.display = 'none';
            }
        }
    </script>
@endsection
