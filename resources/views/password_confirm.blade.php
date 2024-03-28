<x-structure.base title="Confirm Password">
    <div id="invite-box">
        <h2>Password Confirmation</h2>
        <h3>This action requires you to confirm your password, please enter your password below.</h3>
        <div class="button-box">
            <form method="post">
                @csrf
                <input type="password" name="password" autocomplete="current-password" required />
                <x-components.3d_button fg_color="#B1CA65" bg_color="#88A236">Confirm</x-components.3d_button>
            </form>
        </div>
    </div>

    <script src="{{ asset("assets/scripts/disable-elastic-scroll.js") }}"></script>
</x-structure.base>
