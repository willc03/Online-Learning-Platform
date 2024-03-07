<div class="custom-toggle">
    <input @if(isset($name)) name="{{ $name }}" @endif type="checkbox" id="toggle" class="toggle-input" @if(isset($checked)) checked @endif>
    <label for="toggle" class="toggle-label"></label>
</div>
