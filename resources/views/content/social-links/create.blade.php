@extends('layout.layout')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="auth-form-container text-start">
                <div class="col-auto">
                    <h1 class="app-page-title mb-5 ps-2">Add Social Media Link</h1>
                </div>
                <form class="auth-form login-form" action="{{ route('social.links.store') }}" method="POST"
                    id="socialLinkForm">
                    @csrf
                    <input name="created_by" type="hidden" value="{{ Auth::user()->id }}">

                    @if (session('success'))
                        <div class="text-success text-center small pb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="text-danger text-center small pb-3">
                            <p>Something went wrong</p>
                        </div>
                    @endif

                    <!-- Dropdown to select social account -->
                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="social_account" class="ps-2 pb-2">Select Social Account</label>
                            <select id="social_account" name="social_account" class="form-select" required>
                                <option value="">Select an option</option>
                                <option value="email">Email</option>
                                <option value="facebook">Facebook</option>
                                <option value="instagram">Instagram</option>
                                <option value="twitter">Twitter</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                            @if ($errors->has('social_account'))
                                <div class="text-danger small">{{ $errors->first('social_account') }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Input for the selected social account -->
                    <div class="row">
                        <div class="w-100 mb-3">
                            <label for="social_link" class="ps-2 pb-2">Enter Social Link/Details</label>
                            <input id="social_link" name="social_link" type="text" class="form-control"
                                style="padding-top: 7px;" placeholder="Enter link or detail for the selected account"
                                required>
                            <div id="social_link_error" class="text-danger small d-none">Invalid input for the selected
                                account.</div>
                            @if ($errors->has('social_link'))
                                <div class="text-danger small">{{ $errors->first('social_link') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn app-btn-primary theme-btn mx-auto">Add Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('social_account').addEventListener('change', function() {
            const socialInput = document.getElementById('social_link');
            const selectedAccount = this.value;

            // Reset validation
            socialInput.value = '';
            socialInput.type = 'text';
            socialInput.placeholder = 'Enter link or detail for the selected account';
            document.getElementById('social_link_error').classList.add('d-none');

            // Update input type and validation based on selection
            if (selectedAccount === 'email') {
                socialInput.type = 'email';
                socialInput.placeholder = 'Enter email address';
            } else if (selectedAccount === 'whatsapp') {
                socialInput.type = 'tel';
                socialInput.placeholder = 'Enter WhatsApp number';
            }
        });

        document.getElementById('socialLinkForm').addEventListener('submit', function(e) {
            const selectedAccount = document.getElementById('social_account').value;
            const socialInput = document.getElementById('social_link');
            const linkError = document.getElementById('social_link_error');

            // Validate social links for specific platforms
            if (['facebook', 'instagram', 'twitter', 'linkedin'].includes(selectedAccount)) {
                const regexMap = {
                    facebook: /^(https?:\/\/)?(www\.)?facebook\.com\/.+/i,
                    instagram: /^(https?:\/\/)?(www\.)?instagram\.com\/.+/i,
                    twitter: /^(https?:\/\/)?(www\.)?twitter\.com\/.+/i,
                    linkedin: /^(https?:\/\/)?(www\.)?linkedin\.com\/.+/i,
                };
                const isValid = regexMap[selectedAccount].test(socialInput.value);

                if (!isValid) {
                    linkError.textContent = `Invalid ${selectedAccount} link.`;
                    linkError.classList.remove('d-none');
                    e.preventDefault();
                }
            }
        });
    </script>
@endsection
