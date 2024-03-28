<form method="post" action="{{ route('course.settings.invite.create', ['id' => $course->id]) }}" id="new-invite-form">
    @csrf
    <legend>Create a new invite</legend>
    <label>
        Activate invite upon creation:
        <x-components.toggle :name="'active'" :checked="true" />
    </label>

    <fieldset class="middle flex-col">
        <legend>Maximum Uses</legend>
        <label>
            Allow unlimited uses:
            <x-components.toggle :name="'unlimitedUses'" />
        </label>
        <label>
            Set maximum number of uses:
            <input type="number" min="1" name="allowedUses" value="1" style="width: 20%;" />
        </label>
    </fieldset>

    <fieldset class="middle flex-col">
        <legend>Expiry date</legend>
        <label>
            Set invite to never expire:
            <x-components.toggle :name="'neverExpire'" />
        </label>
        <label>
            Set expiry date:
            <input id="expiryDate" type="text" name="expiryDate" value="{{ date("d/m/Y H:i", now()->getTimestamp()) }}" />
        </label>
    </fieldset>

    <x-components.3d_button id="add-invite-btn" class="course-button-mini no-buffer max-content middle" fg_color="#B1CA65" bg_color="#88A236">Submit</x-components.3d_button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <script>
        $("#expiryDate").datetimepicker({
            minDate: 0, defaultTime: "23:59", formatDate: "d/m/Y", formatTime: "H:i", format: "d/m/Y H:i"
        });
    </script>
</form>
