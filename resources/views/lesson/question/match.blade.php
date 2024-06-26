<input type="hidden" name="answer" id="answer" /> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="match-field"> {{-- Display the options to the user --}}
    <legend>Order these items correctly:</legend>

    <style>
        .three-d.selected {
            background-color: #0276aa !important;
        }

        .three-d.selected .foreground {
            background-color: #48b1e1 !important;
        }

        .three-d.incorrect {
            background-color: #CA6565 !important;
        }

        .three-d.incorrect .foreground {
            background-color: #A23636 !important;
        }

        .three-d.correct {
            filter: saturate(100%) !important;
            background-color: #88A236 !important;
        }

        .three-d.correct .foreground {
            filter: saturate(100%) !important;
            background-color: #B1CA65 !important;
            transform: translateY(-2px) translateX(-10px) !important;
        }
    </style>

    @php $sideChoices = [0, 1]; @endphp
    @if ($question->item_value['are_sides_random'])
        @shuffle($sideChoices)
    @endif
    @php $choices = $question->item_value['items_to_match'] @endphp
    @shuffle($choices)
    <div class="left-box">
        @foreach($choices as $choice)
            <x-ui.interactive-button type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $choice[$sideChoices[0]] }}</x-ui.interactive-button>
        @endforeach
    </div>

    @shuffle($choices)
    <div class="right-box">
        @foreach($choices as $choice)
            <x-ui.interactive-button type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $choice[$sideChoices[1]] }}</x-ui.interactive-button>
        @endforeach
    </div>
</fieldset>

<script>
    ajaxRoute = "{{ route("course.lesson.partial", [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}";
</script>
<script src="{{ asset("assets/scripts/question_scripts/match.js") }}"></script>
