<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="single-choice-field"> {{-- Display the options to the user --}}
    <legend>Click your answer(s):</legend>
    <style>
        .option-button.selected {
            background-color: #0276aa!important;
        }
        .option-button.selected .foreground {
            background-color: #48b1e1 !important;
        }
    </style>
    @foreach($choices as $option) {{-- $value is passed in from the question page --}}
        <x-components.3d_button type="button" class="option-button" onclick="onSingleChoiceButtonClick(this)" value="{{ $option }}" fg_color="#81d4fa" bg_color="#5a94af">{{ $option }}</x-components.3d_button>
    @endforeach
</fieldset>

{{-- A submit button will always be displayed, as multiple answers will need to be selected prior --}}
<x-components.3d_button id="submit-question" type="submit" disabled bg_color="#88A236" fg_color="#B1CA65">Submit</x-components.3d_button>

<script> {{-- Write a script to manage the clicking of correct answers --}}
    const buttons = document.querySelectorAll(".option-button");

    function onSingleChoiceButtonClick(selectedObject)
    {
        selectedObject.classList.toggle("selected"); {{-- Toggle the selected class item on each click --}}

        let answer = []; {{-- The answer needs to be made into a JSON string of all selected answers --}}
        buttons.forEach(button => {
            if (button.classList.contains("selected")) {
                answer.push(button.getAttribute("value"));
            }
        });
        document.getElementById("answer").setAttribute("value", JSON.stringify(answer)); {{-- Set the answer hidden input to the value in the button --}}

        {{-- Enable the submit button if it exists --}}
        const submit = document.getElementById("submit-question");
        if (submit && answer.length > 0) {
            submit.disabled = false;
        } else if (submit && !answer.length) {
            submit.disabled = true;
        }
    }
</script>
