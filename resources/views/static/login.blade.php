<x-structure.wrapper title="Login">
    {{-- Make an authentication error message for if they try to access a page that requires login --}}
    @if (session('middleware_auth'))
        <x-message.error title="Authentication Required" description="You must be logged in to access this page!" />
    @endif
    {{-- Now that error messages have been handled, the rest of the page can be rendered --}}
    <div class="message right-float clearfix" id="register-box">
        <h3>Don't have an account?</h3>
        <p><a href="{{ url('/register/') }}">Sign up</a> here to begin learning</p>
    </div>

    <div class="left-float clearfix" id="login-form">
        <h1>Login</h1>
        <p>Signing in to your account will allow you to continue your learning journey.</p>

        <form method="post" action="{{ url('/login/') }}">
            @csrf {{-- The CSRF tag blocks CSRF attacks by including a unique code that has to be read by the server --}}

            {{-- Username --}}
            <label class="form-flex" for="username">
                <span class="required">Username:</span>
                <input class="var-width" type="text" name="username" placeholder="e.g. john.doe" autocomplete="username" required />
            </label>

            {{-- Password --}}
            <label class="form-flex" for="password">
                <span class="required">Password:</span>
                <input class="var-width" type="password" name="password" autocomplete="current-password" required />
            </label>

            {{-- Remember me box --}}
            <label class="form-flex" for="remember">
                <span>Remember me:</span>
                <x-ui.checkbox name="remember" />
            </label>

            {{-- Submit --}}
            <input type="submit" value="Log in" />
        </form>
    </div>

    <script src="{{ asset("assets/scripts/forms/form_validation.js") }}"></script>
</x-structure.wrapper>
