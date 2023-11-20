<x-structure.wrapper title="Question">
    {{-- Show the user the question --}}
    <h3>{{ str_replace("%", "____", $item_title) }}</h3> {{-- Replace any blanks for fill the blanks with four underscores --}}
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

        @endswitch
    {{-- End the form --}}
    </form>
</x-structure.wrapper>
