<x-structure.wrapper title="Register">
    {{-- All error messages will be handled first --}}
    @if (session('validation_error'))
        <x-messages.error title="Validation Error" description="The information submitted did not match the requirements!" />
    @elseif ($errors->get('email'))
        <x-messages.error title="Login Error" description="The e-mail address entered is not registered with us, please try again." />
    @elseif ($errors->get('password'))
        <x-messages.error title="Login Error" description="The password entered is incorrect, please try again." />
    @endif
    {{-- Now that error messages have been handled, the rest of the page can be rendered --}}
    <div class="left-float clearfix" id="login-form">
        <h1>Login</h1>
        <p>Signing in to your account will allow you to continue your learning journey.</p>

        <form method="post" action="{{ url('/login/') }}">
            @csrf {{-- The CSRF tag blocks CSRF attacks by including a unique code that has to be read by the server --}}

            {{-- Email --}}
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="e.g. john.doe@example.com" required>

            {{-- Password --}}
            <label for="password">Password:</label>
            <input type="password" name="password" required>

            {{-- Submit --}}
            <input type="submit">
        </form>
    </div>


        {{-- Submit --}}
        <input type="submit">
    </form>
    <x-scripts.form_validation />
</x-structure.wrapper>
