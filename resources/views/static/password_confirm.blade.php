<x-structure.base title="Confirm Password">
    <div id="invite-box">
        <h2>Password Confirmation</h2>
        <h3>Before you complete this action, you must confirm your password against our records.</h3>
        <p><span class="italicise">This ensures you are completely sure you wish to continue.</span></p>
        <p><span class="italicise">You may be returned to the previous page to continue your action.</span></p>
        <div class="button-box">
            <form method="post">
                @csrf
                <input type="password" name="password" autocomplete="current-password" required />
                <x-ui.checkbox fg_color="#B1CA65" bg_color="#88A236" class="course-button-mini">Confirm</x-ui.checkbox>
            </form>
        </div>
    </div>

    <script src="{{ asset("assets/scripts/disable-elastic-scroll.js") }}"></script>
</x-structure.base>
