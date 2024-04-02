<x-structure.wrapper title="Courses">
    {{-- Display the courses the user is currently taking first --}}
    @auth
        @if(auth()->user()->displayableCourses->count() > 0)
            <h2>Your courses</h2>
            <div class="courses">
                @foreach(auth()->user()->displayableCourses as $course)
                    <div class="course-item">
                        <p class="title">{{ $course->title }}</p>
                        <span class="italicise small-text">By {{ $course->courseOwner->name }}</span>
                        @if ($course->description)
                            <p class="description">{{ $course->description }}</p>
                        @else
                            <br>
                        @endif
                        <x-ui.interactive-button fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini" onclick="location.href = '{{ url(route('course.home', [ 'id' => $course->id ])) }}'">Open course</x-ui.interactive-button>
                    </div>
                @endforeach
            </div>
        @endif
    @endauth

    {{-- Display a box for the use to enter an invite id. --}}
    @auth
        <h2>Have an invitation code?</h2>
        <div class="invite">
            <form id="course-code" action="{{ url('/join') }}" method="get">
                <label class="form-flex var-width">
                    <span style="margin-right: 5px">Enter the code here:</span>
                    <input type="text" name="id" class="var-width" required />
                </label>
                <x-ui.interactive-button role="button" fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini max-content">Submit</x-ui.interactive-button>
            </form>
        </div>
    @endauth
    {{-- Allow the user to create a course --}}
    @auth
        <h2>Create a course</h2>
        <div class="create">
            <p>Do you want to teach others? Click the button below to create a course.</p>
            <x-ui.interactive-button fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini" id="course-create-btn">Create a course</x-ui.interactive-button>
            <form action="{{ route('course.create') }}" method="post" id="create-course" class="flex-col">
                @csrf
                <legend>Create a new course</legend>
                <label class="form-flex">
                    <span class="required">Course title:</span>
                    <input name="title" type="text" required />
                </label>
                <label class="form-flex">
                    <span>Course description:</span>
                    <textarea name="description" placeholder="Enter a description for your course here (optional)"></textarea>
                </label>
                <label class="form-flex">
                    <span class="required">Make your course public?</span>
                    <x-ui.checkbox name="publicity" :checked="true" />
                </label>
                <x-ui.interactive-button fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini max-content middle">Create course</x-ui.interactive-button>
            </form>
        </div>
    @endauth
    {{-- Display public courses --}}
    @if($courses->where('is_public', true)->count() > 0)
        <h2>Available Courses</h2>
        <div class="courses">
            @foreach($courses as $course)
                @if($course->is_public)
                    <div class="course-item">
                        <p class="title">{{ $course->title }}</p>
                        <span class="italicise small-text">By {{ $course->courseOwner->name }}</span>
                        @if ($course->description)
                            <p class="description">{{ $course->description }}</p>
                        @else
                            <br>
                        @endif
                        @if(auth()->user() && auth()->user()->courses->contains('id', $course->id))
                            <x-ui.interactive-button fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini" onclick="location.href = '{{ url(route('course.home', [ 'id' => $course->id ])) }}'">Open course</x-ui.interactive-button>
                        @else
                            <x-ui.interactive-button fg-color="#43AA8B" bg-color="#245B4A" data-url="{{ url(route('join.accept', [ 'id' => $course->id ])) }}" class="join-btn course-button-mini">Join this course</x-ui.interactive-button>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @else
        @auth
            <h2></h2>
            <p>There are no available public courses that you can join at this time.</p>
        @endauth
    @endif

    <script src="{{ asset('assets/scripts/courses/enter_invite.js') }}"></script>
    <script src="{{ asset('assets/scripts/courses/course_create.js') }}"></script>
</x-structure.wrapper>
