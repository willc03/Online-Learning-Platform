<fieldset class="middle">
    <legend>Text component addition</legend>
    <label class="form-flex">
        <span class="required">Enter your text here:</span>
        <textarea name="item-title" required></textarea>
    </label>
    <label class="form-flex">
        <span>Subtext (optional):</span>
        <textarea name="item-description"></textarea>
    </label>
    <x-ui.interactive-button id="add-btn" class="course-button-mini middle" fg-color="#43AA8B" bg-color="#245B4A">Submit new item</x-ui.interactive-button>
</fieldset>
