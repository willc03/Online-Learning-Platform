@php $varUUID = 'X' . str_replace('-', '_', \Illuminate\Support\Str::uuid()->toString()); @endphp

<fieldset class="middle" id="{{ $varUUID }}">
    <input type="hidden" name="unique_anchor" value="{{ $varUUID }}" />
    <input type="hidden" name="item-answers" value="-1" />
    <legend>Item Match Question</legend>
    <label class="form-flex">
        <span class="required">Question text:</span>
        <input type="text" name="item-title" required />
    </label>
    <label class="form-flex">
        <span>Question description:</span>
        <input type="text" name="item-description" />
    </label>
    <label class="form-flex">
        <span class="required">Randomise presentation order:</span>
        <x-ui.checkbox name="item-randomise-sides" />
    </label>

    <fieldset class="middle">
        <legend>Add Match Pairs</legend>
        <p id="match-missing" class="error">Please ensure both match fields have entries.</p>
        <label class="form-flex">
            <span class="required">Match one:</span>
            <input type="text" name="match-one" />
        </label>
        <label class="form-flex">
            <span class="required">Match two:</span>
            <input type="text" name="match-two" />
        </label>
        <x-ui.interactive-button id="make-pair" type="button" class="course-button-mini max-content" fg_color="#43AA8B" bg_color="#245B4A">Add match pair</x-ui.interactive-button>
    </fieldset>

    <fieldset class="middle">
        <legend>Match Pairs</legend>
        <div id="match-pairs">
            <p id="no-pair-msg"><span class="italicise" style="width: 100%!important">There are no pairs to be matched.</span></p>
        </div>
    </fieldset>
    <x-ui.interactive-button type="button" id="submit-btn-match" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Create question</x-ui.interactive-button>
</fieldset>

<div class="template middle answer-row flex-row" style="display: none">
    <p class="var-width answer-text" id="one"></p>
    <p class="var-width answer-text" id="two"></p>
    <x-ui.interactive-button type="button" class="course-button-mini remove-bottom-spacer max-content self-center" id="delete-button" fg-color="#D10023" bg-color="#840016"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
</div>

<script>
    $(function () {
        const errors = {
            matchMissing: $("#{{ $varUUID }} #match-missing")
        };
        const matchContainer = $("#match-pairs");
        let m1 = $("#{{ $varUUID }} input[name='match-one']"), m2 = $("#{{ $varUUID }} input[name='match-two']");

        let answers_{{ $varUUID }} = [];

        $(document).on('click', "#{{ $varUUID }} #make-pair", function () {
            let v1 = $(m1).val(), v2 = $(m2).val();
            if ( !v1 || !v2 ) {
                return;
            }

            // Check for duplicates
            let pairExists = answers_{{ $varUUID }}.some(pair => pair[0] === v1 && pair[1] === v2);
            if ( !pairExists ) {
                answers_{{ $varUUID }}.push([ v1, v2 ]);

                let newAnswer = $(".template").clone().appendTo(matchContainer);
                $(newAnswer)
                    .css('display', '')
                    .removeClass('template')
                    .find("p#one")
                    .text(v1);
                $(newAnswer).find("p#two").text(v2);

                $(newAnswer).find("#delete-button").on('click', function() {
                    for (let i = 0; i < answers_{{ $varUUID }}.length; i++) {
                        if (answers_{{ $varUUID }}[i][0] === v1 && answers_{{ $varUUID }}[i][1] === v2) {
                            answers_{{ $varUUID }}.splice(i, 1);
                            $(newAnswer).remove();
                        }
                    }
                    $("p#no-pair-msg").css('display', $(matchContainer).children().length > 0 ? 'none' : 'block');
                });
                $("p#no-pair-msg").css('display', $(matchContainer).children().length > 0 ? 'none' : 'block');
            } else {
                alert("This match combination already exists.");
            }
        });


        $(m1).add(m2).on("input", function () {
            let v1 = $(m1).val(), v2 = $(m2).val();
            if ( !v1 || !v2 ) {
                errors.matchMissing.css("display", "block");
            } else {
                errors.matchMissing.css("display", "none");
            }
        });

        $(document).on('click', '#{{ $varUUID }} #submit-btn-match', function () {
            // Check form elements are valid
            if ( $("#new-lesson-item").valid() === false ) {
                return;
            }
            // Compile the answers
            let answers = [];
            $(matchContainer).children().each(function () {
                if ( !($(this).find("p#one").text() === "" && $(this).find("p#two").text() === "") ) {
                    answers.push({
                        match_one: $(this).find('p#one').text(), match_two: $(this).find('p#two').text()
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
