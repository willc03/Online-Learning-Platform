<fieldset class="middle">
    <legend>Question addition</legend>
    <label class="form-flex">
        <span class="required">Question type:</span>
        <select name="item-sub-type" id="q-type-selector">
            <option selected disabled value="null">Select a question type</option>
            <option value="single-choice">Single choice</option>
            <option value="multi-choice">Multiple choice</option>
            <option value="fill-in-blanks">Fill in the blanks</option>
            <option value="true-false">True or false</option>
            <option value="order">Order items</option>
            <option value="match">Match items</option>
            <option value="word-search">Word search</option>
        </select>
    </label>
</fieldset>
<div id="question-container"></div>
<script>
    $("#q-type-selector").on('change', function () {
        $("#question-container").empty();
        $.ajax({
            url: "{{ route("course.lesson.configure.form-request", [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}", data: {
                'form-name': $("#q-type-selector").val(), 'form-type': 'question',
            }, success: function (response) {
                $("#question-container").html(response);
            }
        });
    });
</script>
