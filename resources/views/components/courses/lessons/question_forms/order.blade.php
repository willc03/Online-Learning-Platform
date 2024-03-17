@php $varUUID = str_replace('-', '_', \Illuminate\Support\Str::uuid()->toString()); @endphp

<fieldset class="middle">
    <input type="hidden" name="unique_anchor" value="{{ $varUUID }}" />
    <input type="hidden" name="item-answers" />
    {{-- Heading --}}
    <legend>Order Items Question</legend>
    {{-- Question --}}
    <label class="form-flex">
        <span class="required">Question text:</span>
        <input type="text" name="item-title" required>
    </label>
    {{-- Question description (optional) --}}
    <label class="form-flex">
        <span>Question description:</span>
        <textarea name="item-description"></textarea>
    </label>
    {{-- Set order item display direction --}}
    <label class="form-flex">
        <span class="required">Option display order:</span>
        <select name="direction">
            <option selected value="vertical">Vertical</option>
            <option value="horizontal">Horizontal</option>
        </select>
    </label>
    {{-- Field for adding items to order --}}
    <fieldset class="middle">
        <legend>Add items to order</legend>
        <label class="form-flex">
            <span class="required">Item content:</span>
            <input type="text" name="item" id="item-input" />
        </label>
        <x-components.3d_button type="button" id="add-btn-order" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Add item</x-components.3d_button>
    </fieldset>
    {{-- Allow user to order items before submitting --}}
    <fieldset class="middle">
        <legend>Specify correct order</legend>
        <p class="no-items-msg"><span style="width: 100%;" class="italicise">There are no items to be ordered!</span></p>
        <div class="order-items"></div>
    </fieldset>
    {{-- Create question button --}}
    <x-components.3d_button type="button" id="submit-btn-order" class="course-button-mini middle" fg-color="#43AA8B" bg-color="#245B4A">Create question</x-components.3d_button>
</fieldset>

<div class="template middle answer-row flex-row move" style="display: none;">
    <p class="var-width answer-text"></p>
</div>

<script>
    $(function() {
        let answers_{{ $varUUID }} = []
        let answerInputBox = $("#item-input");
        let answerContainer = $(".order-items");
        $(document).on('click', '#add-btn-order', function() {
            if ($("input[name='unique_anchor']").val() !== "{{ $varUUID }}") { return; }
            if ($(answerInputBox).val()) {
                // Check for duplicate answers
                console.log($.inArray( $(answerInputBox).val() , answers_{{ $varUUID }}));
                if ($.inArray( $(answerInputBox).val() , answers_{{ $varUUID }}) !== -1 ) {
                    alert("Cannot add exact duplicates. Please try again.");
                    return;
                }
                // Add the answer if it's not a duplicate
                let newAnswer = $(".template").clone().appendTo(answerContainer);
                $(newAnswer)
                    .css('display', '')
                    .removeClass('template')
                    .find("p")
                    .text($(answerInputBox).val());
                answers_{{ $varUUID }}.push($(answerInputBox).val());
                $("p span.italicise").remove();
            }
        });

        $(answerContainer).sortable({ placeholder: "answer-row middle", axis: "y"});

        $(document).on('input', "select[name='direction']", function() {
            if ($("input[name='unique_anchor']").val() !== "{{ $varUUID }}") { return; }
            $(answerContainer).removeClass($("select[name='direction']").val() === "vertical" ? "flex-row" : "flex-col").addClass($("select[name='direction']").val() === "vertical" ? "flex-col" : "flex-row");
            $(answerContainer).sortable({ axis: $("select[name='direction']").val() === "vertical" ? "y" : "x" });
        })

        $(document).on('click', '#submit-btn-order', function() {
            if ($("input[name='unique_anchor']").val() !== "{{ $varUUID }}") { return; }
            // Ensure there are at least two answers
            if ($(answerContainer).children().length < 2) {
                alert("Please ensure the question has at least two answers to order.");
                return;
            }
            // Check form elements are valid
            if ($("#new-lesson-item").valid() === false) {
                alert("Please ensure the form is correctly filled out before submitting the question.");
                return;
            }
            // Format the answer
            let answer = [];
            $(answerContainer).children().each(function() {
                console.log($(this));
                answer.push({
                    answer: $(this).find('p').text(),
                })
            });
            $("input[name='item-answers']").attr('value', JSON.stringify(answer));
            // Submit the form if all conditions are met
            $('#new-lesson-item').submit();
        });
        // Add rules for form validation
        $("#new-lesson-item").validate({
            rules: { 'item-title': { required: true } },
            messages: { 'item-title': { required: "Please enter the question title" } }
        });
    });
</script>
