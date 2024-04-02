<input type="hidden" name="answer" id="answer" /> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="single-choice-field"> {{-- Display the options to the user --}}
    <legend>Click your answer:</legend>
    <style>
        .option-button.selected {
            background-color: #0276aa !important;
        }

        .option-button.selected .foreground {
            background-color: #48b1e1 !important;
        }
    </style>
    @foreach($question->item_value["question_choices"] as $option)
        {{-- $value is passed in from the question page --}}
        <x-ui.interactive-button class="option-button" value="{{ $option }}" fg_color="#81d4fa" bg_color="#5a94af">{{ $option }}</x-ui.interactive-button>
    @endforeach
</fieldset>

@if(!$question->item_value['one_time_answer'])
    {{-- Display a submit button if they are allowed to change their answer --}}
    <x-ui.interactive-button id="submit-question" type="submit" disabled bg_color="#88A236" fg_color="#B1CA65">Submit</x-ui.interactive-button>
@endif

<script>
    one_time_answer = {{ $question->item_value["one_time_answer"] ? "true" : "false" }};
</script>
<script src="{{ asset("assets/scripts/question_scripts/single_choice.js") }}"></script>
