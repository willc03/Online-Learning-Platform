<x-structure.wrapper title="Register">
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
        <input type="password" name="password" required>

        {{-- Submit --}}
        <input type="submit">
    </form>
</x-structure.wrapper>
