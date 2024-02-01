<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="file" method="post">
    @csrf
    {{-- Fieldsets are then defined for each option --}}
    <fieldset>
        <p>Add a new text object.</p>
        <label class="flex-col">
            Enter your text in the field below:
            <textarea name="content"></textarea>
        </label>
    </fieldset>
</form>
