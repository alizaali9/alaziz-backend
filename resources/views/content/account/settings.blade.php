@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">

            <h1 class="app-page-title">My Account</h1>

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="text-danger text-center small py-3">{{ $error }}</div>
                @endforeach
            @endif

            @if (session('success'))
                <div class="text-success text-center small py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row gy-4">
                <div class="col-12 col-lg-6">
                    <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
                        <div class="app-card-header p-3 border-bottom-0">
                            <div class="row align-items-center gx-3">
                                <div class="col-auto">
                                    <div class="app-icon-holder">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person"
                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                                        </svg>
                                    </div><!--//icon-holder-->

                                </div><!--//col-->
                                <div class="col-auto">
                                    <h4 class="app-card-title">Profile</h4>
                                </div><!--//col-->
                            </div><!--//row-->
                        </div><!--//app-card-header-->
                        <div class="app-card-body px-4 w-100">
                            <div class="item border-bottom py-3">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-auto">
                                        <div class="item-label"><strong>Name</strong></div>
                                        <div class="item-data">{{ Auth::user()->name }}</div>
                                    </div><!--//col-->
                                    <div class="col text-end">
                                        <button class="btn-sm app-btn-secondary" data-bs-toggle="modal"
                                            data-bs-target="#editNameModal">Change</button>
                                    </div><!--//col-->
                                </div><!--//row-->
                            </div><!--//item-->
                            <div class="item border-bottom py-3">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-auto">
                                        <div class="item-label"><strong>Email</strong></div>
                                        <div class="item-data">{{ Auth::user()->email }}</div>
                                    </div><!--//col-->
                                    <div class="col text-end">
                                        <button class="btn-sm app-btn-secondary" data-bs-toggle="modal"
                                            data-bs-target="#editEmailModal">Change</button>
                                    </div><!--//col-->
                                </div><!--//row-->
                            </div><!--//item-->
                            <div class="item border-bottom py-3">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-auto">
                                        <div class="item-label"><strong>Role</strong></div>
                                        <div class="item-data">
                                            @switch(Auth::user()->role)
                                                @case(1)
                                                    Admin
                                                    @break
                                                @case(2)
                                                    Instructor
                                                    @break
                                                @default
                                                Sub Admin
                                            @endswitch
                                        </div>

                                    </div><!--//col-->
                                    <div class="col text-end">
                                    </div><!--//col-->
                                </div><!--//row-->
                            </div><!--//item-->
                            @if (Auth::user()->role == 2)
                                <div class="item border-bottom py-3">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-auto">
                                            <div class="item-label"><strong>About</strong></div>
                                            <div class="item-data">
                                                {{ Str::ucfirst($instructor->about) }}
                                            </div>
                                        </div><!--//col-->
                                        <div class="col text-end">
                                            <button class="btn-sm app-btn-secondary" data-bs-toggle="modal"
                                                data-bs-target="#editAboutModal">Change</button>
                                        </div><!--//col-->
                                    </div><!--//row-->
                                </div><!--//item-->
                                <div class="item border-bottom py-3">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-auto">
                                            <div class="item-label"><strong>Skills</strong></div>
                                            <div class="item-data">
                                                {{ Str::ucfirst($instructor->skills) }}
                                            </div>
                                        </div><!--//col-->
                                        <div class="col text-end">
                                            <button class="btn-sm app-btn-secondary" data-bs-toggle="modal"
                                                data-bs-target="#editSkillsModal">Change</button>
                                        </div><!--//col-->
                                    </div><!--//row-->
                                </div><!--//item-->
                                <div class="item border-bottom py-3">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-auto">
                                            <div class="item-label"><strong>Picture</strong></div>
                                            <div class="item-data">
                                                <img src="{{ asset('storage/' . $instructor->picture) }}"
                                                    alt="{{ $instructor->name }}'s Picture" class="img-thumbnail"
                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                            </div>
                                        </div><!--//col-->
                                        <div class="col text-end">
                                            <button class="btn-sm app-btn-secondary" data-bs-toggle="modal"
                                                data-bs-target="#editPictureModal">Change</button>
                                        </div><!--//col-->
                                    </div><!--//row-->
                                </div><!--//item-->
                            @endif

                        </div><!--//app-card-body-->

                    </div><!--//app-card-->
                </div><!--//col-->
                <div class="col-12 col-lg-6">
                    <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
                        <div class="app-card-header p-3 border-bottom-0">
                            <div class="row align-items-center gx-3">
                                <div class="col-auto">
                                    <div class="app-icon-holder">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-shield-check"
                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M5.443 1.991a60.17 60.17 0 0 0-2.725.802.454.454 0 0 0-.315.366C1.87 7.056 3.1 9.9 4.567 11.773c.736.94 1.533 1.636 2.197 2.093.333.228.626.394.857.5.116.053.21.089.282.11A.73.73 0 0 0 8 14.5c.007-.001.038-.005.097-.023.072-.022.166-.058.282-.111.23-.106.525-.272.857-.5a10.197 10.197 0 0 0 2.197-2.093C12.9 9.9 14.13 7.056 13.597 3.159a.454.454 0 0 0-.315-.366c-.626-.2-1.682-.526-2.725-.802C9.491 1.71 8.51 1.5 8 1.5c-.51 0-1.49.21-2.557.491zm-.256-.966C6.23.749 7.337.5 8 .5c.662 0 1.77.249 2.813.525a61.09 61.09 0 0 1 2.772.815c.528.168.926.623 1.003 1.184.573 4.197-.756 7.307-2.367 9.365a11.191 11.191 0 0 1-2.418 2.3 6.942 6.942 0 0 1-1.007.586c-.27.124-.558.225-.796.225s-.526-.101-.796-.225a6.908 6.908 0 0 1-1.007-.586 11.192 11.192 0 0 1-2.417-2.3C2.167 10.331.839 7.221 1.412 3.024A1.454 1.454 0 0 1 2.415 1.84a61.11 61.11 0 0 1 2.772-.815z" />
                                            <path fill-rule="evenodd"
                                                d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0z" />
                                        </svg>
                                    </div><!--//icon-holder-->

                                </div><!--//col-->
                                <div class="col-auto">
                                    <h4 class="app-card-title">Security</h4>
                                </div><!--//col-->
                            </div><!--//row-->
                        </div><!--//app-card-header-->
                        <div class="app-card-body px-4 w-100">

                            <div class="item border-bottom py-3">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-auto">
                                        <div class="item-label"><strong>Password</strong></div>
                                        <div class="item-data">
                                            ��������
                                        </div>

                                    </div><!--//col-->
                                    <div class="col text-end">
                                        <button class="btn-sm app-btn-secondary" data-bs-toggle="modal"
                                            data-bs-target="#editPasswordModal">Change</button>
                                    </div><!--//col-->
                                </div><!--//row-->
                            </div><!--//item-->
                        </div><!--//app-card-body-->

                    </div><!--//app-card-->
                </div>
            </div><!--//row-->

        </div><!--//container-fluid-->
    </div><!--//app-content-->
    <div class="modal fade" id="editNameModal" tabindex="-1" aria-labelledby="editNameModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editNameForm" method="POST" enctype="multipart/form-data"
                    action="{{ route('updateUserName', ['id' => Auth::user()->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNameModalLabel">Change Your Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quizName" class="form-label text-primary fw-bold">Name</label>
                            <input type="text" class="form-control" id="quizName" name="name"
                                value="{{ Auth::user()->name }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn app-btn-secondary theme-btn"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn app-btn-primary theme-btn">Change Name</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editEmailForm" method="POST" enctype="multipart/form-data"
                    action="{{ route('updateUserEmail', ['id' => Auth::user()->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmailModalLabel">Change Your Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label text-primary fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ Auth::user()->email }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn app-btn-secondary theme-btn"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn app-btn-primary theme-btn">Change Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if (Auth::user()->role == 2)
        <div class="modal fade" id="editAboutModal" tabindex="-1" aria-labelledby="editAboutModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editAboutForm" method="POST" enctype="multipart/form-data"
                        action="{{ route('updateInstructorAbout', ['id' => $instructor->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAboutModalLabel">Change Your About</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="about" class="form-label text-primary fw-bold">About</label>
                                <textarea class="form-control" id="about" name="about">{{ $instructor->about }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn app-btn-secondary theme-btn"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn app-btn-primary theme-btn">Change About</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editSkillsModal" tabindex="-1" aria-labelledby="editSkillsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editSkillsForm" method="POST" enctype="multipart/form-data"
                        action="{{ route('updateInstructorSkills', ['id' => $instructor->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSkillsModalLabel">Change Your Skills</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="skills" class="form-label text-primary fw-bold">Skills</label>
                                <textarea class="form-control" id="skills" name="skills">{{ $instructor->skills }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn app-btn-secondary theme-btn"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn app-btn-primary theme-btn">Change Skills</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editPictureModal" tabindex="-1" aria-labelledby="editSkillsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editSkillsForm" method="POST" enctype="multipart/form-data"
                        action="{{ route('updateInstructorPic', ['id' => $instructor->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSkillsModalLabel">Change Your Skills</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="picture" class="form-label text-primary fw-bold">Change Your Picture</label>
                                <input type="file" name="picture" id="picture" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn app-btn-secondary theme-btn"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn app-btn-primary theme-btn">Change Picture</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editPasswordForm" method="POST"
                    action="{{ route('updatePassword', ['id' => Auth::user()->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPasswordModalLabel">Change Your Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label text-primary fw-bold">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label text-primary fw-bold">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label text-primary fw-bold">Confirm New
                                Password</label>
                            <input type="password" class="form-control" id="confirmPassword"
                                name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn app-btn-secondary theme-btn"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn app-btn-primary theme-btn">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
