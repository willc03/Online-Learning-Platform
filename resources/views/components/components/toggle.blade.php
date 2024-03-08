<div class="custom-toggle">
    @php $toggleId = uuid_create(); @endphp
    <input @if(isset($name)) name="{{ $name }}" @endif type="checkbox" id="toggle-{{ $toggleId }}" class="toggle-input" @if(isset($checked)) checked @endif>
    <label for="toggle-{{ $toggleId }}" class="toggle-label"></label>
</div>
