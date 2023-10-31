<x-structure.wrapper title="Register">
    @if (session('validation_error'))
        @php $error_array = []; @endphp
        @foreach ($errors->getMessages() as $error_type)
            @foreach($error_type as $error_message)
                @php array_push($error_array, $error_message) @endphp
            @endforeach
        @endforeach
        <x-messages.error title="Validation Error" description="" :passed_errors="$error_array" />
    @endif

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
            <label for="firstname">First name:</label>
            <input type="text" name="firstname" required>

            {{-- Last name --}}
            <label for="lastname">Last name:</label>
            <input type="text" name="lastname" required>

            {{-- Email --}}
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            {{-- Password --}}
            <label for="password">Password:</label>
            <input type="password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" id="password" autocomplete="new-password" required>

            {{-- Password confirmation--}}
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" id="password_confirmation" autocomplete="new-password" required>

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
            <input type="submit" value="Sign up">
        </form>
    </div>

    <x-scripts.password_criteria />
    <x-scripts.form_validation />
</x-structure.wrapper>
