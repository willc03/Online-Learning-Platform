const buttons = $(".option-button");

function calculateAnswer() {
    let answer = [];
    $("#question-list button").each(function(index, element) {
        answer.push(element.innerText);
    })
    $("#answer").prop("value", JSON.stringify(answer));
}

$(function() {
    $("#question-list").sortable({
        revert: false,
        cancel: false,
        placeholder: "three-d draggable-choice",
        scrollSensitivity: 100,
        stop: calculateAnswer,
        axis: questionAxis
    });
    $("ul, li").disableSelection();

    calculateAnswer(); // Calculate the initial answer
});
