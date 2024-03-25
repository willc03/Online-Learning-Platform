<x-structure.wrapper title="View Attempts">
    <x-components.3d_button class="course-button-mini max-content" fg-color="#43AA8B" bg-color="#245B4A" onclick="location.href = '{{ route('course.home', ['id' => $course->id]) }}'">Return to course home</x-components.3d_button>
    @if (count($attempts) == 0)
        <p>There are no user attempts for this lesson.</p>
    @else
        <p>This lesson has a maximum score of <span class="italicise">{{ $maxScore }}</span>.</p>
        <p><span class="italicise">Note: Attempts by blocked users have been excluded.</span></p>
        <div class="attempt-table">
            <div class="table-row table-header">
                <div class="table-col">
                    <p>Attempt ID</p>
                </div>
                <div class="table-col">
                    <p>User ID</p>
                </div>
                <div class="table-col">
                    <p>User Name</p>
                </div>
                <div class="table-col">
                    <p>Score</p>
                </div>
            </div>
            @foreach($attempts as $attempt)
                @php
                    $exists = $course->users->contains(function ($user) use ($attempt) {
                        return $user->user_id === $attempt->user_id && !$user->blocked;
                    });
                @endphp
                @if (!($attempt->user_id == $course->owner) && !$exists)
                    @continue
                @endif
                <div class="table-row" id="{{ $attempt->id }}">
                    <div class="table-col">
                        <p>{{ $attempt->id }}</p>
                    </div>
                    <div class="table-col">
                        <p>{{ $attempt->user_id }}</p>
                    </div>
                    <div class="table-col">
                        <p>{{ $attempt->user->name }}</p>
                    </div>
                    <div class="table-col">
                        <p>{{ $attempt->score }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-structure.wrapper>
