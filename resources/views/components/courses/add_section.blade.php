<x-components.3d_button class="course-button-mini" id="add-section-button" fg-color="#43AA8B" bg-color="#245B4A" onclick="$(addSectionForm).animate({height: $(addSectionForm).data('size')})">Add new section</x-components.3d_button>
<form id="course-section-add-form">
    <fieldset class="flex-col">
        <legend>New Section</legend>
        <label class="required" for="title">Section title:</label>
        <input type="text" name="title" required>

        <label for="description">Section description:</label>
        <textarea name="description" id="new-section-description"></textarea>

        <div class="submit-buttons">
            <x-components.3d_button value="false" fg_color="#CA6565" bg_color="#A23636" onclick="$(addSectionForm).animate({height: '0px'}, 500)">Cancel</x-components.3d_button>
            <x-components.3d_button value="true" fg_color="#B1CA65" bg_color="#88A236">Add section</x-components.3d_button>
        </div>
    </fieldset>
</form>
