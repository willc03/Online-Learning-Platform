<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="image" method="post">
    @csrf
    <input type="hidden" name="component-type" value="image">
    <input type="hidden" name="course-id" value="{{ $courseId }}">
    <input type="hidden" name="section-id" value="{{ $sectionId }}">
    <fieldset>
        <label class="flex-col">
            Upload an image here
            <input name="content" type="image" required>
        </label>
    </fieldset>
</form>
