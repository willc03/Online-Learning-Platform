<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="file" method="post">
    @csrf
    <input type="hidden" name="component-type" value="file">
    <input type="hidden" name="course-id" value="{{ $courseId }}">
    <input type="hidden" name="section-id" value="{{ $sectionId }}">
    <fieldset>
        <label>
            Title:
            <input type="text" name="title" required>
        </label>
        <br>
        <label>
            Select a file from those in your course's file store.<br><span class="small-text italicise">Don't see a file you need?</em> <a href="{{ route('course.settings.get', ['id' => $courseId]) }}">Upload a new file</a></span>
            <br><br>
            <select name="file" style="width: 75%; margin: 0 12.5%;" required>
                @foreach($course->files as $entry)
                    <option value="{{ $entry->id }}">{{ $entry->name }} ({{ basename($entry->path) }})</option>
                @endforeach
            </select>
        </label>
        <br>
    </fieldset>
</form>
