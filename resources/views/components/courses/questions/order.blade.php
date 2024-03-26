<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="order-field @if($direction == "vertical") vertical @endif"> {{-- Display the options to the user --}}
    <legend>Order these items correctly:</legend>
    @shuffle($choices)
    <ul id="question-list">
        {{-- Add styling if it's horizontal to size the buttons --}}
        @if($direction == "horizontal")
            <style>
                #question-list {
                    display: flex;
                    justify-content: space-around;
                }
                #question-list button {
                    width: calc({{ 100/count($choices) }}% - 2%)!important;
                }
            </style>
        @endif
        @for($i = 0; $i < count($choices); $i++)
            <x-components.3d_button class="draggable-choice" type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $choices[$i] }}</x-components.3d_button>
        @endfor
    </ul>
</fieldset>

<x-components.3d_button id="submit-question" type="submit" bg_color="#88A236" fg_color="#B1CA65">Submit</x-components.3d_button>

<script>
    questionAxis = @if ($direction == 'horizontal') 'x' @else 'y' @endif ;
</script>
<script src="{{ asset("assets/scripts/question_scripts/order.js") }}"></script>
