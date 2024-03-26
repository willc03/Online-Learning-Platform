<x-structure.wrapper title="Account">
    <h1>Account Settings</h1>
    <span class="italicise">Welcome back, {{ strtok(auth()->user()->name, " ") }}</span>
    <h2>Change Password</h2>
    <x-components.3d_button class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A" id="password-change-button">Change password</x-components.3d_button>
    <form method="post" action="{{ route('account.new-password') }}" class="form-flex" id="change-password">
        <legend>Change password</legend>
        @csrf
        {{-- Current password --}}
        <label class="form-flex">
            <span class="required">Current password:</span>
            <input type="password" name="current-password" autocomplete="current-password" required />
        </label>
        {{-- New password --}}
        <label class="form-flex">
            <span class="required">New password:</span>
            <input type="password" name="new-password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" id="password" autocomplete="new-password" required />
        </label>
        {{-- New password confirmation --}}
        <label class="form-flex">
            <span class="required">Confirm new password:</span>
            <input type="password" name="new-password_confirmation" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" id="password" autocomplete="new-password" required />
        </label>
        {{-- Submit button --}}
        <x-components.3d_button class="course-button-mini middle" fg-color="#43AA8B" bg-color="#245B4A">Submit</x-components.3d_button>
        {{-- Password requirements --}}
        <div id="password-requirements" class="middle">
            <legend>Password requirements</legend>
            <ul>
                <li id="character"><span></span> At least 8 characters</li>
                <li id="uppercase"><span></span> At least 1 upper-case character</li>
                <li id="lowercase"><span></span> At least 1 lower-case character</li>
                <li id="number"><span></span> At least 1 number</li>
                <li id="symbol"><span></span> At least 1 special character</li>
            </ul>
        </div>
    </form>
    <script src="{{ asset("assets/scripts/forms/password_criteria.js") }}"></script>
    <script src="{{ asset("assets/scripts/forms/change-password.js") }}"></script>
</x-structure.wrapper>