<form method="post" action="{{ route('course.settings.invite.create', ['id' => $course->id]) }}" id="new-invite-form">
    @csrf
    <fieldset class="flex-col outer">
        <legend>Create a new invite</legend>
        <label>
            Activate invite upon creation:
            <input type="checkbox" name="active" checked>
        </label>

        <fieldset class="flex-col">
            <legend>Maximum Uses</legend>
            <label>
                Allow unlimited uses:
                <input type="checkbox" name="unlimitedUses">
            </label>
            <label>
                Set maximum number of uses:
                <input type="number" min="0" name="allowedUses" value="0" style="width: 20%;">
            </label>
        </fieldset>

        <fieldset class="flex-col">
            <legend>Expiry date</legend>
            <label>
                Set invite to never expire:
                <input type="checkbox" name="neverExpire">
            </label>
            <label>
                Set expiry date:
                <input type="text" name="expiryDate" value="{{ date("d/m/Y H:i", now()->getTimestamp()) }}">
            </label>
        </fieldset>

        <x-components.3d_button id="add-invite-btn" class="course-button-mini no-buffer max-content" fg_color="#B1CA65" bg_color="#88A236">Submit</x-components.3d_button>
    </fieldset>
</form>
