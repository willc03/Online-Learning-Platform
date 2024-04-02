@php $varUUID = 'X' . str_replace('-', '_', \Illuminate\Support\Str::uuid()->toString()); @endphp

<fieldset class="middle" id="{{ $varUUID }}">
    <input type="hidden" name="unique_anchor" value="{{ $varUUID }}" />
    <legend>True or False Question</legend>
    <label class="form-flex">
        <span class="required">Question text:</span>
        <input type="text" name="item-title" required />
    </label>
    <label class="form-flex">
        <span>Question description:</span>
        <textarea name="item-description"></textarea>
    </label>
    <label class="form-flex">
        <span class="required">Answer is true?</span>
        <x-ui.checkbox name="item-true-or-false" />
    </label>
    <label class="form-flex">
        <span class="required">Allow answer changes:</span>
        <x-ui.checkbox name="item-allow-answer-changes" />
    </label>
    <input type="hidden" name="item-answers" value="-1" />
    <x-ui.interactive-button type="button" id="submit-btn-true-false" class="course-button-mini middle" fg-color="#43AA8B" bg-color="#245B4A">Create question</x-ui.interactive-button>
</fieldset>

<script>
    $(function () {
        $(document).on('click', '#{{ $varUUID }} #submit-btn-true-false', function () {
            // Check form elements are valid
            if ( !$("#new-lesson-item").valid() ) {
                return;
            }
            // Submit the form if all conditions are met
            $('#new-lesson-item').submit();
        });
        // Add rules for form validation
        $("#new-lesson-item").validate({
            rules: { 'item-title': { required: true } }, messages: { 'item-title': { required: "Please enter the question title" } }
        });
    });
</script>
