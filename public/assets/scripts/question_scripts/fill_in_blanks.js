$(function () {
    $.easing.easeOutCubic = function (x, t, b, c, d) { // Define the cubic easing function
        return c * ((t = t / d - 1) * t * t + 1) + b;
    };

    // Define jQuery class and id selectors
    const blankFields = $(".blank");
    const optionButtons = $(".option-button");
    const answerInput = $("#answer");
    const submitInput = $("#submit-question");

    // Define functions, these will not be commented as they are self-explanatory
    function getFirstBlankField ()
    {
        return blankFields.not(".filled").first()[0] || false;
    }

    function getElementDetails (element)
    {
        const $element = $(element);
        return {
            width: $element.width(), height: $element.height(), x: $element.offset().left, y: $element.offset().top,
        };
    }

    // Option button logic
    optionButtons.on("click", function (event) {
        const button = $(event.currentTarget); // Way of retrieving the clicked button in jQuery
        const buttonDetails = getElementDetails(button);

        if ( button.hasClass("block-transitions") ) { // A check for whether the button is a currently selected option
            const filledField = $(".blank[option='" + button.attr("id") + "']");
            filledField.removeClass("filled").attr("option", null);
            button.removeClass("block-transitions").animate({ opacity: 0 }, 100, "easeOutCubic", function () {
                setTimeout(function () {
                    button.css({ position: "", width: "", height: "", left: "", top: "", zIndex: "" })
                        .animate({ opacity: 100 }, 100);
                }, 300, "easeOutCubic");
            });
        } else { // Move the button to the first available option, if available
            const blankField = getFirstBlankField();
            if ( !blankField ) {
                return;
            }

            const fieldDetails = getElementDetails(blankField);

            $(blankField).addClass("filled").attr("option", button.attr("id"));

            $(button).addClass("block-transitions").css({
                position: "absolute", width: buttonDetails.width, height: buttonDetails.height, left: buttonDetails.x, top: buttonDetails.y, zIndex: 2,
            }).animate({
                width: fieldDetails.width, height: fieldDetails.height, left: fieldDetails.x + 1, top: fieldDetails.y + 3,
            }, 500, "easeOutCubic");
        }

        // Recalculate the answer value for the form
        const answer = blankFields.filter(".filled").map(function () {
            return $("#" + $(this).attr("option")).val();
        }).get();

        answerInput.val(JSON.stringify(answer));
        submitInput.prop("disabled", answer.length !== blankFields.length);
    });

    // Window logic, to move the absolute form buttons when the window size is changed.
    $(window).on("resize", function () {
        blankFields.filter(".filled").each(function (_, field) {
            const selectedOption = $(field).attr("option");
            if ( selectedOption ) {
                const optionElement = $("#" + selectedOption);
                const fieldDetails = getElementDetails(field);
                optionElement.css({
                    width: fieldDetails.width, height: fieldDetails.height, left: fieldDetails.x + 1, top: fieldDetails.y + 3,
                });
            }
        });
    });
});
