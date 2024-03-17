<fieldset class="middle">
    <legend>Multiple Choice Question</legend>
    <label class="form-flex">
        <span class="required">Question text:</span>
        <input type="text" name="item-title" required>
    </label>
    <label class="form-flex">
        <span>Question description:</span>
        <textarea name="item-description"></textarea>
    </label>
    <label class="form-flex">
        <span class="required">Add answers:</span>
        <div class="flex-row answer-manager">
            <input class="var-width" type="text" name="answer" id="answer-input">
            <x-components.3d_button type="button" id="add-btn" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Add answer</x-components.3d_button>
        </div>
    </label>
    <div class="s-c-answers"></div>
    <input type="hidden" name="item-answers">
    <x-components.3d_button type="button" id="submit-btn-multi-choice" class="course-button-mini middle" fg-color="#43AA8B" bg-color="#245B4A">Create question</x-components.3d_button>
</fieldset>

<div class="template middle answer-row flex-row" style="display: none">
    <p class="var-width answer-text"></p>
    <x-components.3d_button type="button" class="course-button-mini answer-correct" fg-color="#D10023" bg-color="#840016" data-correct="false">Incorrect</x-components.3d_button>
</div>

<script>
    $(function() {
        let answers = [];
        let answerInputBox = $("#answer-input");
        let answerContainer = $(".s-c-answers");
        $(document).on('click', '#add-btn', function() {
            if ($(answerInputBox).val()) {
                // Check for duplicate answers
                if ($.inArray( $(answerInputBox).val() , answers) !== -1 ) {
                    alert("Cannot use duplicate answers! Please type another answer.");
                    return;
                }
                // Add the answer if it's not a duplicate
                let newAnswer = $(".template").clone().appendTo(answerContainer);
                $(newAnswer)
                    .css('display', '')
                    .removeClass('template')
                    .find("p")
                    .text($(answerInputBox).val());
                answers.push($(answerInputBox).val());
            }
        });
        $(document).on('click', '.answer-correct', function() {
            // Define components
            let button = $(this);
            let foreground = $(button).find(".foreground");
            let correct = $(button).attr('data-correct') === "true";
            // Switch the button when it is clicked
            button.attr('data-correct', "true").stop(true, true).animate({ backgroundColor: !correct ? "#245B4A" : "#840016" }, 500);
            foreground.stop(true, true).animate({ backgroundColor: !correct ? "#43AA8B" : "#D10023" }, 500).text(!correct ? "Correct" : "Incorrect");
            $(button).attr('data-correct', !correct);
        });

        $(document).on('click', '#submit-btn-multi-choice', function() {
            // Check if there is at least one correct answer element
            let hasSufficientAnswers = $(".answer-correct[data-correct='true']").length < 2;
            if (hasSufficientAnswers) {
                alert("Please mark multiple answers as correct. If there is only one correct answer, create a single choice question.");
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
                answer.push({
                    answer: $(this).find('p').text(),
                    isCorrect: $(this).find('button').attr('data-correct') === "true"
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
