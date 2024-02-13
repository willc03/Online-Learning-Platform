<x-structure.wrapper title="Settings">
    <x-components.3d_button class="course-button-mini max-content" fg-color="#9EC5AB" bg-color="#5e9c73" onclick="location.href = '{{ route('course.home', ['id' => $course->id]) }}'">Return to course home</x-components.3d_button>
    <h1>COURSE SETTINGS</h1>
    {{-- The user will be able to change basic details about the course here --}}
    <h2>COURSE DETAILS</h2>
    <div id="course-details" class="flex-col">
        <form method="post" action="{{ route('course.settings.set', ['id' => $course->id]) }}">
            @csrf
            <label class="flex-row">
                <span>Course Title:</span>
                <input name="title" type="text" value="{{ $course->title }}" required>
            </label>
            <label class="flex-row">
                <span>Course Description:</span>
                <textarea name="description" style="resize: none">{{ $course->description }}</textarea>
            </label>
            <label class="flex-row">
                <span>Course Privacy:</span>
                <select name="publicity" required>
                    <option selected value="{{ $course->is_public }}">{{ $course->is_public ? "Public" : "Private" }}</option>
                    <option value="{{ !$course->is_public }}">{{ !$course->is_public ? "Public" : "Private" }}</option>
                </select>
            </label>
            <x-components.3d_button id="details-submit" class="course-button-mini max-content" fg-color="#9EC5AB" bg-color="#5e9c73" disabled>Set new details</x-components.3d_button>
        </form>
    </div>
    {{-- Invitations will be managed here, if the course is private --}}
    <h2>COURSE INVITATIONS</h2>
    <div id="course-invitations" class="flex-col">
        @if($course->is_public)
            <p>Course invitations cannot be configured right now. <span class="italicise">Set the course to private to manage invitations.</span></p>
        @elseif($course->invites->count() == 0)
            <p>This course currently has no active invitations.</p>
            <x-components.3d_button id="new-invite" class="course-button-mini max-content" fg-color="#9EC5AB" bg-color="#5e9c73">Create your first invitation</x-components.3d_button>
        @else

        @endif
    </div>
    <h2>COURSE USERS</h2>
    <div id="course-users" class="flex-col">
        @if($course->users->count() === 0)
            <p>Your course doesn't have any users! Users will appear here when they join the course.</p>
        @else

        @endif
    </div>

    {{-- Scripts --}}
    <script src="{{ asset("assets/scripts/courses/admin/core_edit.js") }}"></script>
</x-structure.wrapper>
