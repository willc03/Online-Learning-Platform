<x-structure.wrapper title="{{ $course_name }}">
    {{-- Course details --}}
    <h1>{{ $course_name }}</h1>
    <p id="course-owner" class="mini-text">By <span class="italicise">{{ $course_owner }}</span></p>
    @if ($course_description !== null)
        <br>
        <p id="course-description">{{ $course_description }}</p>
        <br>
    @endif

    {{-- Quick Access --}}
    <h2>Quick Access</h2>

    {{-- Display all the course content in a downwards fashion --}}
    <h2>Course content</h2>
    @foreach($course_sections as $course_section)
        <div class="section" id="{{ $course_section->id }}">
            <h3>{{ $course_section->title }}</h3>
        </div>
    @endforeach
    {{ $course_sections }}
</x-structure.wrapper>
