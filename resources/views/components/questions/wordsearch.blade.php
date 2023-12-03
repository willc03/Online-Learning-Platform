<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="wordsearch-field"> {{-- Display the options to the user --}}
    <div class="wordsearch">
        @foreach($puzzle->toArray() as $row)
            <div class="row">
                @foreach($row as $letter)
                    <div class="letter">
                        <p>{{ $letter }}</p>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</fieldset>
<fieldset class="word-list wordsearch-field" style="width: 50%; margin-top: 10px; align-self: center">
    <legend>Word list:</legend>
    <div class="word-list">
        @foreach($puzzle->getWordList() as $word)
            <p id="ws-word-{{ strtolower($word->word) }}">{{ $word->word }}</p>
        @endforeach
    </div>
</fieldset>

<div id="cover-box" style="display: none">
    <div class="content">
        <h3 id="title"></h3>
        <p id="info"></p>
        <x-components.3d_button type="button" fg_color="#B1CA65" bg_color="#88A236" onclick="$('#cover-box').css('display', 'none')">Done</x-components.3d_button>
    </div>
</div>

<script>
    wordCount = {{ count( $puzzle->getWordList() ) }};
    ajaxRoute = "{{ route("question.partial") }}";
</script>
<script src="{{ asset("assets/scripts/question_scripts/wordsearch.js") }}"></script>
