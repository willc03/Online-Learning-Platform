@php $varUUID = 'X' . str_replace('-', '_', \Illuminate\Support\Str::uuid()->toString()); @endphp

<fieldset class="middle">
    <input type="hidden" name="unique_anchor" value="{{ $varUUID }}" />
    <input type="hidden" name="item-answers" />
    <input type="hidden" name="item-answer-slots" />
    <legend>Fill in the Blanks Question</legend>

    <p><span class="span-ignore italicise">IMPORTANT: Enter blanks into a question by using the percentage symbol, %.</span></p>
    <label class="form-flex">
        <span class="required">Question text:</span>
        <input type="text" name="item-title" required />
    </label>

    <fieldset class="middle">
        <legend>Configure answers</legend>
        <x-components.3d_button id="reset-answers" class="course-button-mini" type="button" fg_color="#D10023" bg_color="#840016">Reset all answers</x-components.3d_button>
        <label class="form-flex">
            <span class="required">Add blank fill option:</span>
            <input type="text" name="blank-option-add" />
        </label>
        <x-components.3d_button id="add-option" type="button" class="course-button-mini max-content" fg_color="#43AA8B" bg_color="#245B4A">Add option</x-components.3d_button>
    </fieldset>

    <fieldset class="middle">
        <legend>Specify answer order</legend>
        <div class="blank-filler">
            <p id="blank-fill-question-container"></p>
            <fieldset id="answer-box" class="middle">
                <legend>Answers</legend>
                <p class="error" id="insufficient-answers">There are insufficient options, please add more options.</p>
            </fieldset>
        </div>
    </fieldset>
    <x-components.3d_button type="button" id="submit-btn-fill-blanks" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Create question</x-components.3d_button>
</fieldset>

<x-components.3d_button id="template" type="button" class="fill-blank-button course-button-mini" fg_color="#81d4fa" bg_color="#5a94af"></x-components.3d_button>

<script>
    const {{ $varUUID }}_answerContainer = $("fieldset#answer-box");
    let {{ $varUUID }}_answerLogicalContainer = [];
    let {{ $varUUID }}_identification = 0;
    // Easing function
    $.easing.easeOutCubic = function(x, t, b, c, d) { // Define the cubic easing function
        return c * ((t = t / d - 1) * t * t + 1) + b;
    };
    // Function to get the number of blanks
    function getBlankQuantity()
    {
        console.log($("span.blank").length);
        return $("span.blank").length;
    }
    function crossCheckAnswers(answerToCheck)
    {
        for (let i = 0; i < {{ $varUUID }}_answerLogicalContainer.length; i++) {
            if ({{ $varUUID }}_answerLogicalContainer[i] == answerToCheck) {
                return false;
            }
        }
        return true;
    }
    function getFirstBlankField() {
        return $("span.blank").not(".filled").first()[0] || false;
    }
    function getElementDetails(element) {
        const $element = $(element);
        return {
            width: $element.width(),
            height: $element.height(),
            x: $element.offset().left,
            y: $element.offset().top,
        };
    }
    function onOptionButtonClicked(event)
    {
        const button = $(event.currentTarget); // Way of retrieving the clicked button in jQuery
        const buttonDetails = getElementDetails(button);

        if (button.hasClass("block-transitions")) { // A check for whether the button is a currently selected option
            const filledField = $(".blank[option='" + button.attr("id") + "']");
            filledField.removeClass("filled").attr("option", null);
            button.removeClass("block-transitions").animate({ opacity: 0 }, 100, "easeOutCubic", function() {
                setTimeout(function() {
                    button.css({ position: "", width: "", height: "", left: "", top: "", zIndex: "" })
                        .animate({ opacity: 100 }, 100);
                }, 300, "easeOutCubic");
            });
        } else { // Move the button to the first available option, if available
            const blankField = getFirstBlankField();
            if (!blankField) return;

            const fieldDetails = getElementDetails(blankField);

            $(blankField).addClass("filled").attr("option", button.attr("id"));

            $(button).addClass("block-transitions").css({
                position: "absolute",
                width: buttonDetails.width,
                height: buttonDetails.height,
                left: buttonDetails.x,
                top: buttonDetails.y - 4,
                zIndex: 2,
            }).animate({
                width: fieldDetails.width,
                height: fieldDetails.height,
                left: fieldDetails.x + 1,
                top: fieldDetails.y - 4,
            }, 500, "easeOutCubic");
        }
    }
    // Hide the first template button
    $("#template").css('display', 'none');
    // Handle input changes for the item title.
    $("input[name='item-title']").on('input', function() {
        let text = $(this).val();
        // Remake the question holder in the specification paragraph
        $("#blank-fill-question-container").html(text.replace(/\n/g, "<br>").replace(/%/g, "<span class='blank'></span>"));
        // Remove the existing option buttons
        $(".fill-blank-button:not(#template)").remove();
        // Re-add all the options
        for (let i = 0; i < {{ $varUUID }}_answerLogicalContainer.length; i++)
        {
            let newAnswer = $("#template").clone().appendTo({{ $varUUID }}_answerContainer);
            $(newAnswer).attr('id', {{ $varUUID }}_identification).css('display', 'block');
            {{ $varUUID }}_identification++;
            $(newAnswer).find('span').text({{ $varUUID }}_answerLogicalContainer[i]);
            $(newAnswer).on('click', function(event) {
                onOptionButtonClicked.call($(this), event);
            })
        }
        // Change the visibility of the error message
        $("#insufficient-answers").css('display', ( getBlankQuantity() > {{ $varUUID }}_answerLogicalContainer.length ? "block" : "none" ));
    });
    // Handle the addition of options
    $("#add-option").on('click', function() {
        if (!crossCheckAnswers($("input[name='blank-option-add']").val())) {
            return alert("You cannot include exact duplicates as answers!");
        }
        let newAnswer = $("#template").clone().appendTo({{ $varUUID }}_answerContainer);
        $(newAnswer).attr('id', {{ $varUUID }}_identification).css('display', 'block');
        {{ $varUUID }}_identification++;
        $(newAnswer).find('span').text($("input[name='blank-option-add']").val());
        {{ $varUUID }}_answerLogicalContainer.push($("input[name='blank-option-add']").val());
        $(newAnswer).on('click', function(event) {
            onOptionButtonClicked.call($(this), event);
        })
        // Change the visibility of the error message
        $("#insufficient-answers").css('display', ( getBlankQuantity() > {{ $varUUID }}_answerLogicalContainer.length ? "block" : "none" ));
    });
    // Handle answer resets
    $("#reset-answers").on('click', function() {
        {{ $varUUID }}_answerLogicalContainer = [];
        $(".fill-blank-button:not(#template)").remove();
        $("#insufficient-answers").css('display', "block");
        {{ $varUUID }}_identification = 0;
    });
    // Handle the clicking of options to fill blanks
    // Handle question creation requests
    $("#submit-btn-fill-blanks").on('click', function() {
        // Validate the form
        if (!$("#new-lesson-item").valid()) {
            return;
        }
        // Check there is at least one blank
        if (getBlankQuantity() < 1) {
            return alert("Please ensure there is at least one blank to be filled in.");
        }
        // Check to ensure there are enough answers
        if ({{ $varUUID }}_answerLogicalContainer.length < getBlankQuantity()) {
            return alert("There are insufficient answers. Please add more answers.");
        }
        // Check the blanks are filled
        if ($("span.blank.filled").length < getBlankQuantity()) {
            return alert("Please ensure all blank spaces are filled in.");
        }
        // Calculate the answer
        let answerArray = [];
        let index = 0;
        $("span.blank.filled").each(function() {
            let button = $(".fill-blank-button[id='" + $(this).attr('option') + "']");
            answerArray.push({ position: index++, answer: $(button).find('.foreground').text() });
        });
        $("input[name='item-answers']").val(JSON.stringify(answerArray));
        // Select ALL possible answers
        let possibleAnswers = [];
        $(".fill-blank-button:not(#template)").each(function() {
            possibleAnswers.push($(this).find(".foreground").text());
        })
        $("input[name='item-answer-slots']").val(JSON.stringify(possibleAnswers));
        // Submit the request
        $("#new-lesson-item").submit();
    })
    // Form validation
    $("#new-lesson-item").validate({
        rules: { 'item-title': { required: true } },
        messages: { 'item-title': { required: "Please enter the question title" } }
    });
    // Window logic, to move the absolute form buttons when the window size is changed.
    $(window).on("resize", function() {
        $("span.blank").filter(".filled").each(function(_, field) {
            const selectedOption = $(field).attr("option");
            if (selectedOption) {
                const optionElement = $("#" + selectedOption);
                const fieldDetails = getElementDetails(field);
                optionElement.css({
                    width: fieldDetails.width,
                    height: fieldDetails.height,
                    left: fieldDetails.x + 1,
                    top: fieldDetails.y + 3,
                });
            }
        });
    });
</script>
<script src="{{ asset("assets/scripts/question_scripts/fill_in_blanks.js") }}"></script>
