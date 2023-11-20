<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="fill-blanks-field"> {{-- Display the options to the user --}}
    <legend>Fill in the blank(s):</legend>
    <style>
        .option-button.selected {
            background-color: #0276aa!important;
        }
        .option-button.selected .foreground {
            background-color: #48b1e1 !important;
        }
    </style>
    @foreach($choices as $option) {{-- $value is passed in from the question page --}}
        <x-components.3d_button type="button" class="option-button" value="{{ $option }}" fg_color="#81d4fa" bg_color="#5a94af">{{ $option }}</x-components.3d_button>
    @endforeach
</fieldset>

{{-- A submit button will always be displayed, as the user may change their mind --}}
<x-components.3d_button id="submit-question" type="submit" disabled bg_color="#88A236" fg_color="#B1CA65">Submit</x-components.3d_button>
