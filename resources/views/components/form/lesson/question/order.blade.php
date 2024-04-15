@php $varUUID = 'X' . str_replace('-', '_', \Illuminate\Support\Str::uuid()->toString()); @endphp

<fieldset class="middle" id="{{ $varUUID }}">
    <input type="hidden" name="unique_anchor" value="{{ $varUUID }}" />
    <input type="hidden" name="item-answers" />
    {{-- Heading --}}
    <legend>Order Items Question</legend>
    {{-- Question --}}
    <label class="form-flex">
        <span class="required">Question text:</span>
        <input type="text" name="item-title" required />
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
        <x-ui.interactive-button type="button" id="add-btn-order" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Add item</x-ui.interactive-button>
    </fieldset>
    {{-- Allow user to order items before submitting --}}
    <fieldset class="middle">
        <legend>Specify correct order</legend>
        <p class="no-items-msg"><span style="width: 100%;" class="italicise">There are no items to be ordered!</span></p>
        <div class="order-items"></div>
    </fieldset>
    {{-- Create question button --}}
    <x-ui.interactive-button type="button" id="submit-btn-order" class="course-button-mini middle" fg-color="#43AA8B" bg-color="#245B4A">Create question</x-ui.interactive-button>
</fieldset>

<div class="template middle answer-row flex-row move" style="display: none;">
    <p class="var-width answer-text"></p>
    <x-ui.interactive-button type="button" class="course-button-mini remove-bottom-spacer max-content self-center" id="delete-button" fg-color="#D10023" bg-color="#840016"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
</div>

<script>
    $(function () {
        let answers_{{ $varUUID }} = []
        let answerInputBox = $("#{{ $varUUID }} #item-input");
        let answerContainer = $("#{{ $varUUID }} .order-items");
        $(document).on('click', '#{{ $varUUID }} #add-btn-order', function () {
            let answerText = $(answerInputBox).val();
            if ( answerText ) {
                // Check for duplicate answers
                console.log($.inArray(answerText, answers_{{ $varUUID }}));
                if ( $.inArray($(answerInputBox).val(), answers_{{ $varUUID }}) !== -1 ) {
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
                answers_{{ $varUUID }}.push(answerText);
                $(newAnswer).find("#delete-button").on('click', function() {
                    for (let i = 0; i < answers_{{ $varUUID }}.length; i++) {
                        if (answers_{{ $varUUID }}[i] === answerText) {
                            answers_{{ $varUUID }}.splice(i, 1);
                            $(newAnswer).remove();
                        }
                    }
                    $("p span.italicise").css('display', $(answerContainer).children().length > 0 ? 'none' : 'block');
                });
                $("p span.italicise").css('display', $(answerContainer).children().length > 0 ? 'none' : 'block');
            }
        });

        $(answerContainer).sortable({ placeholder: "answer-row middle", axis: "y" });

        $(document).on('input', "#{{ $varUUID }} select[name='direction']", function () {
            $(answerContainer).removeClass($("#{{ $varUUID }} select[name='direction']").val() === "vertical" ? "flex-row" : "flex-col").addClass($("#{{ $varUUID }} select[name='direction']").val() === "vertical" ? "flex-col" : "flex-row");
            $(answerContainer).sortable({ axis: $("#{{ $varUUID }} select[name='direction']").val() === "vertical" ? "y" : "x" });
        })

        $(document).on('click', '#{{ $varUUID }} #submit-btn-order', function () {
            // Ensure there are at least two answers
            if ( $(answerContainer).children().length < 2 ) {
                alert("Please ensure the question has at least two answers to order.");
                return;
            }
            // Check form elements are valid
            if ( $("#new-lesson-item").valid() === false ) {
                alert("Please ensure the form is correctly filled out before submitting the question.");
                return;
            }
            // Format the answer
            let answer = [];
            $(answerContainer).children().each(function () {
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
            rules: { 'item-title': { required: true } }, messages: { 'item-title': { required: "Please enter the question title" } }
        });
    });
</script>
