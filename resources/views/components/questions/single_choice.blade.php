{{-- Add a hidden attribute for the final submitted value --}}

<input type="hidden" name="answer" id="answer">
{{-- Display the options to the user --}}
<fieldset class="single-choice-field">
    <legend>Click your answer:</legend>
    @foreach($choices as $option) {{-- $value is passed in from the question page --}}
        <button
            class="option-button"
            onclick="onSingleChoiceButtonClick(this, {{ $option }})"
            @if(!$oneTimeAnswer) type="button" @endif {{-- This line will make it so the form submits on button press only if they can't change their answer --}}
        >
            {{ $option }}
        </button>
    @endforeach
</fieldset>
{{-- Display a submit button if they are allowed to change their answer --}}
@if(!$oneTimeAnswer)
    <input type="submit" id="submit-question" disabled>
@endif


{{-- Write a script to manage the clicking of correct answers --}}

<script>
    function onSingleChoiceButtonClick(selectedObject, optionValue)
    {
        {{-- Get all the buttons --}}
        const buttons = document.querySelectorAll(".option-button");
        {{-- Loop through all the buttons and remove the selected attribute --}}
        buttons.forEach(button => {
            button.classList.remove("selected");
        })
        {{-- Add the selected attribute to the clicked button --}}
        selectedObject.classList.add("selected");
        {{-- Set the answer hidden input to the value in the button --}}
        document.getElementById("answer").setAttribute("value", optionValue);
        {{-- Enable the submit button if it exists --}}
        const submit = document.getElementById("submit-question");
        if (submit) {
            submit.disabled = false
        }
    }
</script>
