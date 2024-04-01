@php $varUUID = 'X' . str_replace('-', '_', \Illuminate\Support\Str::uuid()->toString()); @endphp

<fieldset class="middle" id="{{ $varUUID }}">
    <input type="hidden" name="unique_anchor" value="{{ $varUUID }}" />
    <input type="hidden" name="item-answers" value="-1" />
    <legend>Word Search Question</legend>
    <label class="form-flex">
        <span class="required">Question text:</span>
        <input type="text" name="item-title" required />
    </label>
    <label class="form-flex">
        <span>Question description:</span>
        <input type="text" name="item-description" />
    </label>

    <fieldset class="middle">
        <legend>Add Words</legend>
        <p id="words-missing" class="error">Please ensure both the word and message fields are filled in.</p>
        <label class="form-flex">
            <span class="required">Word:</span>
            <input type="text" name="word" placeholder="This will be displayed on the search grid." />
        </label>
        <label class="form-flex">
            <span class="required">Message when found:</span>
            <textarea name="message" placeholder="The message in this box will be displayed to students when they correctly highlight the word."></textarea>
        </label>
        <x-components.3d_button id="make-word" type="button" class="course-button-mini max-content" fg_color="#43AA8B" bg_color="#245B4A">Add word</x-components.3d_button>
    </fieldset>

    <fieldset class="middle">
        <legend>Words</legend>
        <div id="word-display">
            <p id="no-pair-msg"><span class="italicise" style="width: 100%!important">There are no words submitted.</span></p>
        </div>
    </fieldset>
    <x-components.3d_button type="button" id="submit-btn-word-search" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Create question</x-components.3d_button>
</fieldset>

<div class="template middle answer-row flex-row" style="display: none">
    <p class="var-width answer-text" id="one"></p>
    <p class="var-width answer-text" id="two"></p>
    <x-components.3d_button type="button" class="course-button-mini remove-bottom-spacer max-content self-center" id="delete-button" fg-color="#D10023" bg-color="#840016"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-components.3d_button>
</div>

<script>
    $(function () {
        const errors = {
            wordsMissing: $("#{{ $varUUID }} #words-missing")
        };
        const matchContainer = $("#{{ $varUUID }} #word-display");
        let m1 = $("#{{ $varUUID }} input[name='word']"), m2 = $("#{{ $varUUID }} textarea[name='message']");

        $(document).on('click', "#{{ $varUUID }} #make-word", function () {
            let v1 = $(m1).val(), v2 = $(m2).val();
            if ( !v1 || !v2 ) {
                return;
            }

            // Check for duplicates
            let newAnswer = $(".template").clone().appendTo(matchContainer);
            $(newAnswer)
                .css('display', '')
                .removeClass('template')
                .find("p#one")
                .text(v1);
            $(newAnswer).find("p#two").text(v2);

            $(newAnswer).find("#delete-button").on('click', function() {
                $(newAnswer).remove();
                $("p#no-pair-msg").css('display', $(matchContainer).children().length > 0 ? 'none' : 'block');
            });
            $("p#no-pair-msg").css('display', $(matchContainer).children().length > 0 ? 'none' : 'block');
        });


        $(m1).add(m2).on("input", function () {
            let v1 = $(m1).val(), v2 = $(m2).val();
            if ( !v1 || !v2 ) {
                errors.wordsMissing.css("display", "block");
            } else {
                errors.wordsMissing.css("display", "none");
            }
        });

        $(document).on('click', '#{{ $varUUID }} #submit-btn-word-search', function () {
            // Check form elements are valid
            if ( $("#new-lesson-item").valid() === false ) {
                return;
            }
            // Compile the answers
            let answers = [];
            $(matchContainer).children().each(function () {
                if ( !($(this).find("p#one").text() === "" && $(this).find("p#two").text() === "") ) {
                    answers.push({
                        word: $(this).find('p#one').text(), message: $(this).find('p#two').text()
                    });
                }
            });
            $("#{{ $varUUID }} input[name='item-answers']").attr('value', JSON.stringify(answers));
            // Submit the form if all conditions are met
            $('#new-lesson-item').submit();
        });
        // Add validation rules for if there are less than two matches

        // Add rules for form validation
        $("#new-lesson-item").validate({
            rules: { 'item-title': { required: true } }, messages: { 'item-title': { required: "Please enter the question title" } }
        });
    });
</script>
