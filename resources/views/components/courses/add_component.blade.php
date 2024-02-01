<label class="flex-col">
    Which type of component would you like to add?
    <select name="type">
        <option value="" disabled selected>Please select a component</option>
        <option value="text">Text</option>
        <option value="lesson">Lesson</option>
        <option value="image">Image</option>
        <option value="file">File</option>
    </select>
</label>

<div id="form_container"></div>

<script>
    $(function() {
        // Show the selected fieldset when the dropdown changes
        $('select[name="type"]').change(function() {
            $("#form_container").empty();
            $.ajax({
                url: "{{ route("course.getForm", ['id' => $courseId]) }}",
                data: { form_type: $('select[name="type"]').val() },
                success: function(data) {
                    $("#form_container").html(data);
                }
            })
        });
    });
</script>
