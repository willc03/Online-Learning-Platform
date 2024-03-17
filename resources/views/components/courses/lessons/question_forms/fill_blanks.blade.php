@php $varUUID = str_replace('-', '_', \Illuminate\Support\Str::uuid()->toString()); @endphp

<fieldset class="middle">
    <input type="hidden" name="unique_anchor" value="{{ $varUUID }}" />
    <legend>Fill in the Blanks Question</legend>
</fieldset>
