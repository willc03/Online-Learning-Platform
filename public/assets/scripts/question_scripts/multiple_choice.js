const buttons = document.querySelectorAll(".option-button");

function onSingleChoiceButtonClick(selectedObject)
{
    selectedObject.classList.toggle("selected"); // Toggle the selected class item on each click

    let answer = []; // The answer needs to be made into a JSON string of all selected answers
    buttons.forEach(button => {
        if (button.classList.contains("selected")) {
            answer.push(button.getAttribute("value"));
        }
    });
    document.getElementById("answer").setAttribute("value", JSON.stringify(answer)); // Set the answer hidden input to the value in the button

    // Enable the submit button if it exists
    const submit = document.getElementById("submit-question");
    if (submit && answer.length > 0) {
        submit.disabled = false;
    } else if (submit && !answer.length) {
        submit.disabled = true;
    }
}
