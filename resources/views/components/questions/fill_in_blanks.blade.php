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
        $.easing.easeOutCubic = function(x, t, b, c, d) { {{-- Define the cubic easing function --}}
            return c * ((t = t / d - 1) * t * t + 1) + b;
        };

        {{-- Define jQuery class and id selectors --}}
        const blankFields = $(".blank");
        const optionButtons = $(".option-button");
        const answerInput = $("#answer");
        const submitInput = $("#submit-question");

        {{-- Define functions, these will not be commented as they are self-explanatory --}}
        function getFirstBlankField() {
            return blankFields.not(".filled").first()[0] || false;
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

        {{-- Option button logic --}}
        optionButtons.on("click", function(event) {
            const button = $(event.currentTarget); {{-- Way of retrieving the clicked button in jQuery --}}
            const buttonDetails = getElementDetails(button);

            if (button.hasClass("block-transitions")) { {{-- A check for whether the button is a currently selected option --}}
                const filledField = $(".blank[option='" + button.attr("id") + "']");
                filledField.removeClass("filled").attr("option", null);
                button.removeClass("block-transitions").animate({ opacity: 0 }, 100, "easeOutCubic", function() {
                    setTimeout(function() {
                        button.css({ position: "", width: "", height: "", left: "", top: "", zIndex: "" })
                            .animate({ opacity: 100 }, 100);
                    }, 300, "easeOutCubic");
                });
            } else { {{-- Move the button to the first available option, if available --}}
                const blankField = getFirstBlankField();
                if (!blankField) return;

                const fieldDetails = getElementDetails(blankField);

                $(blankField).addClass("filled").attr("option", button.attr("id"));

                $(button).addClass("block-transitions").css({
                    position: "absolute",
                    width: buttonDetails.width,
                    height: buttonDetails.height,
                    left: buttonDetails.x,
                    top: buttonDetails.y,
                    zIndex: 2,
                }).animate({
                    width: fieldDetails.width,
                    height: fieldDetails.height,
                    left: fieldDetails.x + 1,
                    top: fieldDetails.y + 3,
                }, 500, "easeOutCubic");
            }

            {{-- Recalculate the answer value for the form --}}
            const answer = blankFields.filter(".filled").map(function() {
                return $("#" + $(this).attr("option")).val();
            }).get();

            answerInput.val(JSON.stringify(answer));
            submitInput.prop("disabled", answer.length !== blankFields.length);
        });

        {{-- Window logic, to move the absolute form buttons when the window size is changed. --}}
        $(window).on("resize", function() {
            blankFields.filter(".filled").each(function(_, field) {
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
    });
</script>
