<input type="hidden" name="answer" id="answer" /> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="boolean-field"> {{-- Display the options to the user --}}
    <legend>True or false?</legend>
    <style>
        .option-button.selected {
            background-color: #0276aa !important;
        }

        .option-button.selected .foreground {
            background-color: #48b1e1 !important;
        }
    </style>
    <x-ui.interactive-button class="option-button" fg_color="#B1CA65" bg_color="#88A236" value="true">True</x-ui.interactive-button>
    <x-ui.interactive-button class="option-button" fg_color="#CA6565" bg_color="#A23636" value="false">False</x-ui.interactive-button>
</fieldset>

@if(!$question->item_value['one_time_answer'])
    {{-- Display a submit button if they are allowed to change their answer --}}
    <x-ui.interactive-button id="submit-question" type="submit" disabled bg_color="#88A236" fg_color="#B1CA65">Submit</x-ui.interactive-button>
@endif

<script>
    one_time_answer = {{ $question->item_value["one_time_answer"] ? "true" : "false" }};
</script>
<script src="{{ asset("assets/scripts/question_scripts/true_false.js") }}"></script>
