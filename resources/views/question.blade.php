<x-structure.wrapper title="Question">
    {{-- Show the user the question --}}
    <h3 class="question-title">{!! str_replace("\\n", "<br>", (str_replace("%", '<span class=\'blank\'> </span>', $item_title))) !!}</h3> {{-- The exclaimation marks stop the content from being escaped --}}
    {{-- Make the question a submittable form --}}
    <form method="post" action="{{ route('question.answer') }}" class="question-form">
        @csrf
        <input type="hidden" id="question_id" name="question_id" value="{{ $id }}">
        {{-- Produce different results based on the question type --}}
        @switch($item_value['question_type'])

            @case("single_choice")
                <x-questions.single_choice :choices="$item_value['question_choices']" :one_time_answer="$item_value['one_time_answer']" />
                @break

            @case("multiple_choice")
                <x-questions.multiple_choice :choices="$item_value['question_choices']" />
                @break

            @case("fill_in_blanks")
                <x-questions.fill_in_blanks :choices="$item_value['question_choices']" />
                @break

            @case("true_or_false")
                <x-questions.true_false :one-time-answer="$item_value['one_time_answer']" />
                @break

            @case("order")
                <x-questions.order :choices="$item_value['answer_slots']" :direction="$item_value['direction']" />
                @break

            @case("match")
                <x-questions.match :choices="$item_value['items_to_match']" :is_random="$item_value['are_sides_random']" />
                @break

            @case("wordsearch")
                @php
                    $words = [];
                    $maxLength = 0;
                    foreach($item_value['words'] as $wordSet) {
                        if (!empty($wordSet)) {
                            $words[] = $wordSet[0]; // Insert the word on the next slot of the array
                            if (strlen($wordSet[0]) > $maxLength) {
                                $maxLength = strlen($wordSet[0]);
                            }
                        }
                    }
                    $wordsearch = WordSearch\Factory::create($words, $maxLength + 5);
                @endphp
                <x-questions.wordsearch :puzzle="$wordsearch" />
                @break

        @endswitch
    {{-- End the form --}}
    </form>
</x-structure.wrapper>
