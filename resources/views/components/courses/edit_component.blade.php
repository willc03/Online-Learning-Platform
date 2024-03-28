<form id="edit-section-form" method="post" action="{{ route('course.edit', ['id' => $course->id]) }}" class="flex-col">
    @csrf
    <legend>Edit section details</legend>
    <input type="hidden" name="edit_type" value="section_edit" />
    <input type="hidden" name="course_id" value="{{ $course->id }}" />
    <input type="hidden" name="section_id" value="{{ $section->id }}" />
    <label class="form-flex">
        <span>Section Title:</span>
        <input name="title" type="text" value="{{ $section->title }}" required />
    </label>
    <label class="form-flex">
        <span>Section Description:</span>
        <textarea name="description" style="resize: none">{{ $section->description ?? "" }}</textarea>
    </label>
    <x-components.3d_button id="section-details-submit" class="course-button-mini max-content" fg-color="#43AA8B" bg-color="#245B4A" disabled>Set new details</x-components.3d_button>
</form>
