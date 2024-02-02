<label>
    Which type of component would you like to add?
    <select name="type" class="type">
        <option value="" disabled selected>Please select a component</option>
        <option value="text">Text</option>
        <option value="lesson">Lesson</option>
        <option value="image">Image</option>
        <option value="file">File</option>
    </select>
</label>

<div id="form_container" style="display: none"></div>

<div id="submission" class="flex-row" style="display: none">
    <x-components.3d_button id="cancel" class="max-content course-button-mini" fg_color="#CA6565" bg_color="#A23636">Cancel</x-components.3d_button>
    <x-components.3d_button id="submit" class="max-content course-button-mini" fg_color="#B1CA65" bg_color="#88A236">Submit</x-components.3d_button>
</div>
