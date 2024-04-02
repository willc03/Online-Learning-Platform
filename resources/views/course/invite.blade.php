<x-structure.base title="Invite">
    @if(!$success)
        <div id="invite-box">
            <h3>Invite Unavailable</h3>
            <p>{{ $errorMessage }}</p>
            <x-ui.interactive-button id="return-home" fg_color="#B1CA65" bg_color="#88A236" onclick="location.href = '{{ url(route('home')) }}'">Return to home</x-ui.interactive-button>
        </div>
    @else
        <div id="invite-box">
            <h2>Course Invitation</h2>
            <h3>You have received an invite to <strong>{{ $content->title }}</strong></h3>
            <p>Choose one of the options below to accept or decline the invitation.</p>
            <div class="button-box">
                <x-ui.interactive-button id="return-home" fg_color="#CA6565" bg_color="#A23636" onclick="location.href = '{{ url(route('home')) }}'">Return to home</x-ui.interactive-button>
                <form method="post" action="{{ url(route('join.accept')) }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $_GET['id'] }}" />
                    <x-ui.interactive-button id="accept" fg_color="#B1CA65" bg_color="#88A236" onclick="location.href = '{{ url(route('home')) }}'">Accept</x-ui.interactive-button>
                </form>
            </div>
        </div>
    @endif

    <script src="{{ asset("assets/scripts/disable-elastic-scroll.js") }}"></script>
</x-structure.base>
