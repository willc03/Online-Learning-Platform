<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="lesson" method="post">
    @csrf
    {{-- Fieldsets are then defined for each option --}}
    <fieldset>
        <label class="flex-col">
            Lesson title:
            <input name="title" type="text" required>
        </label>
        <label class="flex-col">
            Lesson description:
            <textarea name="description"></textarea>
        </label>
    </fieldset>
</form>
