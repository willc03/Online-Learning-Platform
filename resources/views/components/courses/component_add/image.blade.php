<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="image" method="post">
    @csrf
    {{-- Fieldsets are then defined for each option --}}
    <fieldset>
        <label class="flex-col">
            Upload an image here
            <input name="content" type="image" required>
        </label>
    </fieldset>
</form>
