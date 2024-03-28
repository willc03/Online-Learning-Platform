/*
    This script was derived from the tutorial on HTML form validation by
    Rupert (2017)
*/
const inputs = document.querySelectorAll("input");

function validateInputField (input)
{
    if ( input.checkValidity() ) {
        input.classList.remove("invalid-element");
    } else {
        input.classList.add("invalid-element");
    }
}

inputs.forEach((input) => {
    input.addEventListener("input", () => {
        if ( input.checkValidity() ) {
            validateInputField(input);
        }
    });
    input.addEventListener("blur", () => {
        validateInputField(input);
    });
});
