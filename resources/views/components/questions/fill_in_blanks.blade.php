<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="fill-blanks-field"> {{-- Display the options to the user --}}
    <legend>Fill in the blank(s):</legend>
    <style>
        .option-button.selected {
            background-color: #0276aa!important;
        }
        .option-button.selected .foreground {
            background-color: #48b1e1 !important;
        }
    </style>
    @php $buttonId = 0; @endphp
    @foreach($choices as $option) {{-- $value is passed in from the question page --}}
        <x-components.3d_button id="button_{{ $buttonId++ }}" type="button" class="option-button" value="{{ $option }}" fg_color="#81d4fa" bg_color="#5a94af">{{ $option }}</x-components.3d_button>
    @endforeach
</fieldset>

{{-- A submit button will always be displayed, as the user may change their mind --}}
<x-components.3d_button id="submit-question" type="submit" disabled bg_color="#88A236" fg_color="#B1CA65">Submit</x-components.3d_button>

{{-- Make a script to manage the filling of the blanks --}}
<script>
    $(function() {
        // Add cubic easing
        $.easing.easeOutCubic = function (x, t, b, c, d) {
            return c*((t=t/d-1)*t*t + 1) + b;
        }
        // Define initial variables
        const blankFields = $(".blank");
        const optionButtons = $(".option-button");
        const answerInput = $("#answer");
        const submitInput = $("#submit-question")

        // Functions
        function getFirstBlankField() {
            return $(blankFields).not(".filled").first()[0] ?? false;
        }
        function getElementDetails(element) {
            return {
                width: $(element).width(),
                height: $(element).height(),
                x: $(element).offset().left,
                y: $(element).offset().top,
            } ?? false;
        }

        // Logic
        // Option button logic
        optionButtons.on("click", function(event) {
            // Button details
            let button = event.currentTarget;
            let buttonDetails = getElementDetails(button);

            // Blank field details
            let blankField = getFirstBlankField();
            if (!blankField) { return; }

            let fieldDetails = getElementDetails(blankField);

            // Change field class
            $(blankField).addClass("filled").attr("option", button.id);

            // Change button class
            $(button).addClass("block-transitions");

            // Change CSS
            $(button).css({
                position: "absolute",
                width: buttonDetails.width,
                height: buttonDetails.height,
                left: buttonDetails.x,
                top: buttonDetails.y,
                zIndex: 2,
            })

            // Animate new button location
            $(button).animate({
                width: fieldDetails.width,
                height: fieldDetails.height,
                left: fieldDetails.x + 1,
                top: fieldDetails.y + 3
            }, 500, "easeOutCubic")

            // Animate the blank box disappearing
            $(blankField).animate({
                opacity: 0
            }, 500)

            // Recalculate the answer
            let answer = [];
            blankFields.each(function(_, field) {
                let selectedOption = $(field).attr("option") || false;
                if (selectedOption) {
                    let optionElement = $("#" + selectedOption)[0] || false;
                    if (optionElement) {
                        answer.push(optionElement.value);
                    }
                }
            });
            answerInput.attr("value", JSON.stringify(answer));

            $(submitInput)[0].disabled = answer.length !== $(blankFields).length;
        });

        $(window).on("resize", function() { {{-- The absolute elements must be moved when the window is resized! --}}
            blankFields.each(function(_, field) {
                let selectedOption = $(field).attr("option") || false;
                if (selectedOption) {
                    let optionElement = $("#" + selectedOption)[0] || false;
                    if (optionElement) {
                        let fieldDetails = getElementDetails(field);
                        $(optionElement).css({
                            width: fieldDetails.width,
                            height: fieldDetails.height,
                            left: fieldDetails.x + 1,
                            top: fieldDetails.y + 3
                        });
                    }
                }
            });
        })
    });
</script>
