@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start" style="height: 71vh">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Create Course Parts</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('courses.storeParts') }}" method="POST">
                    @csrf
                    <input name="course_id" type="hidden" value="{{ $courseid }}">
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
                    <div id="input-containers">
                        <div class="row justify-content-center input-container">
                            <div class="w-50 mb-3">
                                <input id="name-0" name="name[0]" type="text" class="form-control signin-email"
                                    placeholder="Course Part Name">
                                @foreach ($errors->get('name.*') as $key => $errorMessages)
                                    @foreach ($errorMessages as $message)
                                        <div class="text-danger small">{{ $message }}</div>
                                    @endforeach
                                @endforeach

                            </div>
                            <div style="width: 5%">
                                <button type="button" class="btn app-btn-primary theme-btn mx-auto"
                                    style="padding-inline: 14px;" onclick="cloneContainer()">+
                                </button>
                            </div>
                            <div style="width: 5%; display: none;">
                                <button type="button" class="btn app-btn-danger theme-btn mx-auto"
                                    onclick="removeContainer(this)">-
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Submit
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
            inputDiv.className = 'w-50 mb-3';
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `name[${containerIndex}]`;
            input.id = `name-${containerIndex}`;
            input.className = 'form-control signin-email';
            input.placeholder = 'Course Part Name';
            inputDiv.appendChild(input);

            const addButtonDiv = document.createElement('div');
            addButtonDiv.style.width = '5%';
            const addButton = document.createElement('button');
            addButton.type = 'button';
            addButton.className = 'btn app-btn-primary theme-btn mx-auto';
            addButton.style.paddingInline = "14px";
            addButton.onclick = cloneContainer;
            addButton.textContent = '+';
            addButtonDiv.appendChild(addButton);

            const removeButtonDiv = document.createElement('div');
            removeButtonDiv.style.width = '5%';
            // removeButtonDiv.style.display = 'none';
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
                if (containers.length == 1) {
                    lastContainer.children[1].style.display = 'block';
                    lastContainer.children[2].style.display = 'none';
                } else {
                    lastContainer.children[1].style.display = 'block';
                    lastContainer.children[2].style.display = 'block';
                }
            }
        }
    </script>
@endsection
