<x-structure.wrapper title="{{ $lesson->title }}">
    <div class="progression">
        <h3>Lesson Progress</h3>
        <p class="progress-bar middle"><span class="percentage" style="width: calc({{ max(0, min(99, $percentage)) }}% - 10px);">{{ max(0, min(99, $percentage)) }}%</span></p>
        <p class="exp">XP Points:<span class="exp-counter">{{ session()->get('lesson.xp') }}</span></p>
        <p class="streak">Answer Streak Multiplier:<span class="streak-counter">{{ is_int(session()->get('lesson.streak')) ? session()->get('lesson.streak') . ".0" : session()->get('lesson.streak') }}x</span></p>
        <form method="post" action="{{ route("course.lesson.end", [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}">
            @csrf
            <x-ui.interactive-button class="max-content course-button-mini db-bs" fg_color="#D10023" bg_color="#840016">End lesson</x-ui.interactive-button>
        </form>
    </div>

    @if($question->item_type == "TEXT")
        <div class="middle lesson-text-holder" style="margin-top: 15px">
            <h2>{{ $question->item_title }}</h2>
            <div class="main">
                @if($question->description)
                    <h3>{{ $question->description }}</h3>
                @endif

                <form method="post" action="{{ route('course.lesson.answer', [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}">
                    @csrf
                    <input type="hidden" id="question_id" name="question_id" value="{{ $question->id }}" />
                    <x-ui.interactive-button id="submit-question" class="course-button-mini max-content middle" type="submit" bg_color="#88A236" fg_color="#B1CA65">{{ session('lesson.position') == session('lesson.max_position') ? "Finish lesson" : "Next" }}</x-ui.interactive-button>
                </form>
            </div>
        </div>
    @elseif($question->item_type == "QUESTION")
        {{-- Show the user the question --}}
        <h3 class="question-title">{!! str_replace("\\n", "<br>", (str_replace("%", '<span class=\'blank\'> </span>', $question->item_title))) !!}</h3> {{-- The exclaimation marks stop the content from being escaped --}}
        {{-- Show an error message if the answer is wrong --}}
        @if(session('error') && session('error') == 'wrong-answer')
            <x-message.error title="Incorrect Answer" description="Don't worry, have another go!" />
            <br>
        @endif
        {{-- Show the description, if one is available --}}
        @if($question->description)
            <p>{{ $question->description }}</p>
        @endif
        {{-- Make the question a submittable form --}}
        <form method="post" action="{{ route('course.lesson.answer', [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}" class="question-form">
            @csrf
            <input type="hidden" id="question_id" name="question_id" value="{{ $question->id }}" />
            {{-- Produce different results based on the question type --}}
            @switch($question->item_value['question_type'])

                @case("single_choice")
                    @include("lesson.question.single-choice")
                    @break

                @case("multiple_choice")
                    @include("lesson.question.multiple-choice")
                    @break

                @case("fill_in_blanks")
                    @include("lesson.question.fill-blanks")
                    @break

                @case("true_or_false")
                    @include("lesson.question.true-false")
                    @break

                @case("order")
                    @include("lesson.question.order")
                    @break

                @case("match")
                    @include("lesson.question.match")
                    @break

                @case("wordsearch")
                    @php
                        $words = [];
                        $maxLength = 0;
                        foreach($question->item_value['words'] as $wordSet) {
                            if (!empty($wordSet)) {
                                $words[] = $wordSet[0]; // Insert the word on the next slot of the array
                                if (strlen($wordSet[0]) > $maxLength) {
                                    $maxLength = strlen($wordSet[0]);
                                }
                            }
                        }
                        $wordsearch = WordSearch\Factory::create($words, $maxLength + 5);
                    @endphp
                    @include("lesson.question.word-search", [ 'puzzle' => $wordsearch ])
                    @break

            @endswitch
            {{-- End the form --}}
        </form>
    @endif
</x-structure.wrapper>


<script>
    $(document).ready(function () {
        var formSubmitted = false;

        // Set flag when form is submitted
        $('form').submit(function () {
            formSubmitted = true;
        });

        // Warn user if they try to leave the page without submitting the form
        $(window).on('beforeunload', function () {
            if ( !formSubmitted ) {
                return 'Are you sure you want to leave this page?';
            }
        });
    });
</script>
