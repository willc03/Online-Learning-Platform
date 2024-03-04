<x-structure.wrapper title="{{ $lesson->title }}">
    {{-- Show the user the question --}}
    <h3 class="question-title">{!! str_replace("\\n", "<br>", (str_replace("%", '<span class=\'blank\'> </span>', $question->item_title))) !!}</h3> {{-- The exclaimation marks stop the content from being escaped --}}
    {{-- Show an error message if the answer is wrong --}}
    @if(session('error') && session('error') == 'wrong-answer')
        <x-messages.error title="Incorrect Answer" description="Don't worry, have another go!"/>
        <br>
    @endif
    {{-- Make the question a submittable form --}}
    <form method="post" action="{{ route('course.lesson.answer', [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}"
          class="question-form">
        @csrf
        <input type="hidden" id="question_id" name="question_id" value="{{ $question->id }}">
        {{-- Produce different results based on the question type --}}
        @switch($question->item_value['question_type'])

            @case("single_choice")
                <x-questions.single_choice :choices="$question->item_value['question_choices']"
                                           :one_time_answer="$question->item_value['one_time_answer']"
                                           :course="$course"
                                           :lesson="$lesson"/>
                @break

            @case("multiple_choice")
                <x-questions.multiple_choice :choices="$question->item_value['question_choices']"
                                             :course="$course"
                                             :lesson="$lesson"/>
                @break

            @case("fill_in_blanks")
                <x-questions.fill_in_blanks :choices="$question->item_value['question_choices']"
                                           :course="$course"
                                           :lesson="$lesson"/>
                @break

            @case("true_or_false")
                <x-questions.true_false :one-time-answer="$question->item_value['one_time_answer']"
                                        :course="$course"
                                        :lesson="$lesson"/>
                @break

            @case("order")
                <x-questions.order :choices="$question->item_value['answer_slots']"
                                   :direction="$question->item_value['direction']"
                                   :course="$course"
                                   :lesson="$lesson"/>
                @break

            @case("match")
                <x-questions.match :choices="$question->item_value['items_to_match']"
                                   :is_random="$question->item_value['are_sides_random']"
                                   :course="$course"
                                   :lesson="$lesson"/>
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
                <x-questions.wordsearch :puzzle="$wordsearch"
                                        :course="$course"
                                        :lesson="$lesson"/>
                @break

        @endswitch
        {{-- End the form --}}
    </form>
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
