{{-- Add a hidden attribute for the final submitted value --}}

<input type="hidden" name="answer" id="answer">
{{-- Display the options to the user --}}
<fieldset class="single-choice-field">
    <legend>Click your answer:</legend>
    @foreach($choices as $option) {{-- $value is passed in from the question page --}}
        <x-components.3d_button class="option-button" onclick="onSingleChoiceButtonClick(this, {{ $option }})">{{ $option }}</x-components.3d_button>
    @endforeach
</fieldset>
{{-- Display a submit button if they are allowed to change their answer --}}
@if(!$oneTimeAnswer)
    <input type="submit" id="submit-question" disabled>
@endif


{{-- Write a script to manage the clicking of correct answers --}}

<script>
    {{-- Get all the buttons --}}
    const buttons = document.querySelectorAll(".option-button");

    function onSingleChoiceButtonClick(selectedObject, optionValue)
    {
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

    @if(!$oneTimeAnswer)
        buttons.forEach(button => {
            button.setAttribute("type", "button");
        })
    @endif
</script>
