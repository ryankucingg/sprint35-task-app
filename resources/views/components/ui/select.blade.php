@props(['id', 'data', 'value','option', 'label', 'message','disabled'])

<fieldset class="fieldset mb-3">
    <legend class="fieldset-legend text-xs font-semibold text-base-content/70 uppercase tracking-wide">{{ $label }}</legend>
    <select class="select select-bordered w-full text-sm transition-all duration-200
        focus:select-primary focus:shadow-sm
        @error($id) select-error @enderror
        @if(isset($disabled)) opacity-60 bg-base-200 @endif"
            @if(isset($disabled)) disabled @endif
            wire:model="{{ $id }}" wire:change="selectChangeValue($event.target.value,'{{ $id }}')">
        @foreach($data as $d)
            <option value="{{ $d[$value] }}">{{ $d[$option] }}</option>
        @endforeach
    </select>
    @error($id)
    <p class="text-error text-xs mt-1 flex items-center gap-1">
        <span class="material-icons text-xs">error</span>
        {{ $message }}
    </p>
    @enderror
</fieldset>
