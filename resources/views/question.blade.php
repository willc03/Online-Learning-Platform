<x-structure.wrapper title="Question">
    {{-- Show the user the question --}}
    <h3 class="question-title">{!! str_replace("\\n", "<br>", (str_replace("%", '<span class=\'blank\'> </span>', $item_title))) !!}</h3> {{-- The exclaimation marks stop the content from being escaped --}}
    {{-- Make the question a submittable form --}}
    <form method="post" action="{{ route('question.answer') }}" class="question-form">
        @csrf
        <input type="hidden" name="question_id" value="{{ $id }}">
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

        @endswitch
    {{-- End the form --}}
    </form>
</x-structure.wrapper>
