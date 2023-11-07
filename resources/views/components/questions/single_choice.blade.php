<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="single-choice-field"> {{-- Display the options to the user --}}
    <legend>Click your answer:</legend>
    <style>
        .option-button.selected {
            background-color: #0276aa!important;
        }
        .option-button.selected .foreground {
            background-color: #48b1e1 !important;
        }
    </style>
    @foreach($choices as $option) {{-- $value is passed in from the question page --}}
        <x-components.3d_button class="option-button" onclick="onSingleChoiceButtonClick(this, {{ $option }})" fg_color="#81d4fa" bg_color="#5a94af">{{ $option }}</x-components.3d_button>
    @endforeach
</fieldset>

@if(!$oneTimeAnswer) {{-- Display a submit button if they are allowed to change their answer --}}
    <input type="submit" id="submit-question" disabled>
@endif

<script> {{-- Write a script to manage the clicking of correct answers --}}
    const buttons = document.querySelectorAll(".option-button");

    function onSingleChoiceButtonClick(selectedObject, optionValue)
    {
        buttons.forEach(button => button.classList.remove("selected")); {{-- Loop through the buttons and remove the 'selected' attribute --}}
        selectedObject.classList.add("selected"); {{-- Add the 'selected' attribute to the clicked button --}}
        document.getElementById("answer").setAttribute("value", optionValue); {{-- Set the answer hidden input to the value in the button --}}

        {{-- Enable the submit button if it exists --}}
        const submit = document.getElementById("submit-question");
        if (submit) {
            submit.disabled = false
        }
    }

    @if(!$oneTimeAnswer) {{-- Set the button to not submit the form if it's set that way when called (unable to do this within the component) --}}
        buttons.forEach(button => button.setAttribute("type", "button"));
    @endif
</script>
