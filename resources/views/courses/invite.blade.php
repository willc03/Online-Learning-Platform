<x-structure.base title="Invite">
    @if(!$success)
        <div id="invalid-invite-box">
            <h1>404</h1>
            <h2>Invite Unavailable</h2>
            <p>{{ $errorMessage }}</p>
            <x-components.3d_button id="return-home" fg_color="#B1CA65" bg_color="#88A236" onclick="location.href = '{{ url(route('home')) }}'">Return to home</x-components.3d_button>
        </div>
    @else

    @endif

    <script>
        $(document.body).css('overflow', 'hidden');
    </script>
</x-structure.base>
