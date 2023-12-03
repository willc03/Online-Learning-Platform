const buttons = $(".option-button");
if(!one_time_answer) {
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
}
