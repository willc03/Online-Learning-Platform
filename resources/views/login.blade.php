<x-structure.wrapper title="Register">
    @if (session('validation_error'))
        <x-messages.error title="Validation Error" description="The information submitted did not match the requirements!" />
    @endif

    <h1>Login</h1>
    <p>Signing in to your account will allow you to continue your learning journey.</p>

    <div class="message login-box">
        <h3>Don't have an account?</h3>
        <p><a href="{{ url('/register/') }}">Sign up</a> here to begin learning</p>
    </div>

    <form method="post" action="{{ url('/login/') }}">
        @csrf {{-- The CSRF tag blocks CSRF attacks by including a unique code that has to be read by the server --}}

        {{-- Email --}}
        <label for="email">Email:</label>
        <input type="email" name="email" required>

        {{-- Password --}}
        <label for="password">Password:</label>
        <input type="password" name="password" required>

        {{-- Submit --}}
        <input type="submit">
    </form>
</x-structure.wrapper>
