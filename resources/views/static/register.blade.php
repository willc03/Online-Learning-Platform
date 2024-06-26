<x-structure.wrapper title="Register">
    <div class="message login-box right-float clearfix" id="login-box">
        <h3>Already have an account?</h3>
        <p><a href="{{ url('/login/') }}">Log in</a> here to access your courses</p>
    </div>

    <div class="left-float clearfix" id="register-form">
        <h1>Register for an account</h1>
        <p>Registering for an account will allow you to begin your learning journey by joining and completing courses made by other users.</p>

        <form method="post" action="{{ url('/register') }}">
            @csrf {{-- The CSRF tag blocks CSRF attacks by including a unique code that has to be read by the server --}}

            {{-- First name --}}
            <label class="required" for="firstname">First name:</label>
            <input type="text" name="firstname" required />

            {{-- Last name --}}
            <label class="required" for="lastname">Last name:</label>
            <input type="text" name="lastname" required />

            {{-- User name --}}
            <label class="required" for="username">Username:</label>
            <input type="text" name="username" required maxlength="20" minlength="4" />

            {{-- Email --}}
            <label class="required" for="email">Email:</label>
            <input type="email" name="email" required />

            {{-- Password --}}
            <label class="required" for="password">Password:</label>
            <input type="password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" id="password" autocomplete="new-password" required />

            {{-- Password confirmation--}}
            <label class="required" for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" id="password_confirmation" autocomplete="new-password" required />

            {{-- Password requirements --}}
            <div id="password-requirements">
                <h4>Password requirements</h4>
                <ul>
                    <li id="character"><span></span> At least 8 characters</li>
                    <li id="uppercase"><span></span> At least 1 upper-case character</li>
                    <li id="lowercase"><span></span> At least 1 lower-case character</li>
                    <li id="number"><span></span> At least 1 number</li>
                    <li id="symbol"><span></span> At least 1 special character</li>
                </ul>
            </div>

            {{-- Submit --}}
            <input type="submit" value="Sign up" />
        </form>
    </div>

    <script src="{{ asset("assets/scripts/forms/password_criteria.js") }}"></script>
    <script src="{{ asset("assets/scripts/forms/form_validation.js") }}"></script>
</x-structure.wrapper>
