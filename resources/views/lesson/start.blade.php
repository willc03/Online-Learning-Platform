<x-structure.base title="{{ $lesson->title }}">
    <style>
        .centre {
            display: flex;
            flex-direction: column;
            width: 100vw;
            height: 50vh;
            align-items: center;
            justify-content: center;
        }
    </style>
    @if($errors->any())
        <x-messages.error title="Unexpected Error" description="" :passed_errors="$errors->all()" />
    @endif
    <div class="centre">
        <h1>{{ $course->title }}</h1>
        <div id="invite-box">
            <h2>{{ $lesson->title }}</h2>
            @if ($lesson->description != null)
                <p>{{ $lesson->description }}</p>
            @endif
            <div class="button-box">
                <x-components.3d_button id="return-home" fg_color="#CA6565" bg_color="#A23636" onclick="location.href = '{{ url(route('course.home', [ 'id' => $course->id ])) }}'">Return to course home</x-components.3d_button>
                <form method="post" action="{{ url(route('course.lesson.answer', [ 'id' => $course->id, 'lessonId' => $lesson->id ])) }}">
                    @csrf
                    <x-components.3d_button id="accept" fg_color="#B1CA65" bg_color="#88A236">Begin lesson</x-components.3d_button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset("assets/scripts/disable-elastic-scroll.js") }}"></script>
</x-structure.base>
