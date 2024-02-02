<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="file" method="post">
    @csrf
    <input type="hidden" name="component-type" value="file">
    <input type="hidden" name="course-id" value="{{ $courseId }}">
    <input type="hidden" name="section-id" value="{{ $sectionId }}">
    <fieldset>
        <p>Don't have a file you need? <a href="{{ route('course.settings', ['id' => $courseId]) }}">Upload a new file</a></p>
        <label class="flex-col">
            Choose a file to display:
            <input name="content" type="file">
        </label>
    </fieldset>
</form>
