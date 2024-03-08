<x-structure.wrapper title="Home">
    {{-- Display the courses the user is currently taking first --}}
    @auth
        @if(auth()->user()->displayableCourses->count() > 0)
            <h2>Your courses</h2>
            <div class="courses">
                @foreach(auth()->user()->displayableCourses as $course)
                    <div class="course-item">
                        <p class="title">{{ $course->title }}</p>
                        @if ($course->courseOwner) {{-- Check if courseOwner exists --}}
                        <span class="italicise small-text">By {{ $course->courseOwner->name }}</span>
                        @else
                            <span class="italicise small-text">By Unknown</span> {{-- Show some default message --}}
                        @endif
                        @if ($course->description)
                            <p class="description">{{ $course->description }}</p>
                        @else
                            <br>
                        @endif
                        <x-components.3d_button fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini" onclick="location.href = '{{ url(route('course.home', [ 'id' => $course->id ])) }}'">Open course</x-components.3d_button>
                    </div>
                @endforeach
            </div>
        @endif
    @endauth


    <h1>Welcome to {{ env('APP_NAME') }}</h1>
    <p>This application is the product of a level 6 dissertation project made by Will Corkill at UCLan.</p>
    <div id="purpose">
        <h2>What is the purpose of {{ env('APP_NAME') }}?</h2>
        <p>The purpose of this project is to provide an easy-to-use and lightweight solution for online learning.</p>
        <p>The designed platform is intended to be easily usable by users of all ages, from primary school students to adult education.</p>
    </div>
    <div id="features">
        <h2>What is supported?</h2>
        <p>Currently, the following is supported:</p>
        <ul>
            <li>Users can create their own courses</li>
            <li>Users can join or be invited to other user's courses</li>
            <li>Blended VLE and online learning environment provides tutelage and assessment</li>
            <li>Fully customisable courses allow organisation according to however user's wish to design their courses</li>
            <li>Public and private course settings to prevent influx of unknown users</li>
            <li>Toggleable administration view to avoid clustered course pages when not needed</li>
            <li>Robust security features built around the Laravel framework</li>
        </ul>
    </div>

    <script src="{{ asset('assets/scripts/courses/course_create.js') }}"></script>
</x-structure.wrapper>
