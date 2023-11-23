<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="boolean-field"> {{-- Display the options to the user --}}
    <legend>True or false?</legend>
    <style>
        .option-button.selected {
            background-color: #0276aa!important;
        }
        .option-button.selected .foreground {
            background-color: #48b1e1 !important;
        }
    </style>
    <x-components.3d_button class="option-button" value="true" fg_color="#B1CA65" bg_color="#88A236">True</x-components.3d_button>
    <x-components.3d_button class="option-button" value="false" fg_color="#CA6565" bg_color="#A23636">False</x-components.3d_button>
</fieldset>

@if(!$oneTimeAnswer) {{-- Display a submit button if they are allowed to change their answer --}}
    <x-components.3d_button id="submit-question" type="submit" disabled bg_color="#88A236" fg_color="#B1CA65">Submit</x-components.3d_button>
@endif

<script> {{-- Write a script to manage the clicking of correct answers --}}
    const buttons = $(".option-button");
    @if(!$oneTimeAnswer)
        buttons.prop("type", "button");

    $(document).ready(function() {
        $(".option-button").on("click", function() {
            $(".option-button").removeClass("selected");
            $(this).addClass("selected");

            const answerValue = $(this).attr("value");
            $("#answer").val(answerValue);

            // Enable/disable submit button
            $("#submit-question").prop("disabled", !answerValue);
        });
    });
    @endif
</script>
