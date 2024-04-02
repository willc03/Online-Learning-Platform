<input type="hidden" name="answer" id="answer" /> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="order-field @if($question->item_value["direction"] == "vertical") vertical @endif"> {{-- Display the options to the user --}}
    <legend>Order these items correctly:</legend>
    @php $choices = $question->item_value["answer_slots"] @endphp
    @shuffle($choices)
    <ul id="question-list">
        {{-- Add styling if it's horizontal to size the buttons --}}
        @if($question->item_value["direction"] == "horizontal")
            <style>
                #question-list {
                    display: flex;
                    justify-content: space-around;
                }

                #question-list button {
                    width: calc({{ 100/count($question->item_value["answer_slots"]) }}% - 2%) !important;
                }
            </style>
        @endif
        @foreach($choices as $answerSlot)
            <x-ui.interactive-button class="draggable-choice" type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $answerSlot }}</x-ui.interactive-button>
        @endforeach
    </ul>
</fieldset>

<x-ui.interactive-button id="submit-question" type="submit" bg_color="#88A236" fg_color="#B1CA65">Submit</x-ui.interactive-button>

<script>
    questionAxis = @if ($question->item_value["direction"] == 'horizontal') 'x'
    @else 'y' @endif ;
</script>
<script src="{{ asset("assets/scripts/question_scripts/order.js") }}"></script>
