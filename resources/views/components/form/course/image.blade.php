<form action="{{ route('course.edit', ['id' => $courseId]) }}" id="add-component-form" class="image" method="post">
    @csrf
    <input type="hidden" name="component-type" value="image" />
    <input type="hidden" name="course-id" value="{{ $courseId }}" />
    <input type="hidden" name="section-id" value="{{ $sectionId }}" />
    <fieldset>
        <label class="flex-col">
            Alt text:
            <textarea name="alt"></textarea>
            <span class="italicise small-text">Provide valuable context for when the image isn't able to be loaded or the user is using a screen reader.</span>
        </label>
        <br>
        <label>
            Select a file from those in your course's file store.<br><span class="small-text italicise">Don't see a file you need? <a href="{{ route('course.settings.get', ['id' => $courseId]) }}">Upload a new file</a>.</span>
            <br><br>
            <select name="image" style="width: 75%; margin: 0 12.5%;" required>
                @foreach($course->files as $entry)
                    @php
                        $fileTypeWhitelist = ['image/png', 'image/jpeg', 'image/gif', 'image/bmp','image/svg+xml'];
                        $fileType = mime_content_type(storage_path('app/courses/' . $entry->path));
                    @endphp
                    @if (in_array($fileType, $fileTypeWhitelist))
                        <option value="{{ $entry->id }}">{{ $entry->name }} ({{ basename($entry->path) }})</option>
                    @endif
                @endforeach
            </select>
        </label>
        <br>
    </fieldset>
</form>
