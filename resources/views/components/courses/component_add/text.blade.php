<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="text" method="post">
    @csrf
    <input type="hidden" name="component-type" value="text">
    <fieldset>
        <label class="flex-col">
            Enter your text in the field below:
            <textarea name="content"></textarea>
        </label>
    </fieldset>
</form>
