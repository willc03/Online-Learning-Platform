<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="lesson" method="post">
    @csrf
    <input type="hidden" name="component-type" value="lesson">
    <input type="hidden" name="course-id" value="{{ $courseId }}">
    <input type="hidden" name="section-id" value="{{ $sectionId }}">
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
