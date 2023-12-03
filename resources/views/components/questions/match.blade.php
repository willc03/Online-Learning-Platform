<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="match-field"> {{-- Display the options to the user --}}
    <legend>Order these items correctly:</legend>

    <style>
        .three-d.selected {
            background-color: #0276aa!important;
        }
        .three-d.selected .foreground {
            background-color: #48b1e1 !important;
        }

        .three-d.incorrect {
            background-color: #CA6565!important;
        }
        .three-d.incorrect .foreground {
            background-color: #A23636!important;
        }

        .three-d.correct {
            filter: saturate(100%)!important;
            background-color: #88A236!important;
        }
        .three-d.correct .foreground {
            filter: saturate(100%)!important;
            background-color: #B1CA65!important;
            transform: translateY(-2px)!important;
        }
    </style>

    @php $sideChoices = [0, 1]; @endphp
    @if ($isRandom)
        @shuffle($sideChoices)
    @endif
    @shuffle($choices)
    <div class="left-box">
        @foreach($choices as $choice)
            <x-components.3d_button type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $choice[$sideChoices[0]] }}</x-components.3d_button>
        @endforeach
    </div>

    @shuffle($choices)
    <div class="right-box">
        @foreach($choices as $choice)
            <x-components.3d_button type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $choice[$sideChoices[1]] }}</x-components.3d_button>
        @endforeach
    </div>
</fieldset>

<script>
    ajaxRoute = "{{ route("question.partial") }}";
</script>
<script src="{{ asset("assets/scripts/question_scripts/match.js") }}"></script>
