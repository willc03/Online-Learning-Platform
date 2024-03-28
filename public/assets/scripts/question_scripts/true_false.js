const buttons = $(".option-button");
if ( !one_time_answer ) {
    buttons.prop("type", "button");

    $(document).ready(function () {
        $(".option-button").on("click", function () {
            $(".option-button").removeClass("selected");
            $(this).addClass("selected");

            const answerValue = $(this).attr("value");
            $("#answer").val(answerValue);

            // Enable/disable submit button
            $("#submit-question").prop("disabled", !answerValue);
        });
    });
} else {
    let pressed = false;
    $("form").on('submit', function (e) {
        if ( !pressed ) {
            e.preventDefault();
        }
    });

    $(".option-button").on("click", function () {
        const answerValue = $(this).attr("value");
        $("#answer").attr('value', answerValue);
        console.log($(this).attr('value'));
        pressed = true;
        setTimeout(function () {
            $("form").submit();
        }, 100); // Adjust the delay time as needed
    });
}
