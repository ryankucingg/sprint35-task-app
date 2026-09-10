@props(['id', 'label', 'placeholder', 'message'])

<fieldset class="fieldset mb-3">
    <legend class="fieldset-legend text-xs font-semibold text-base-content/70 uppercase tracking-wide">{{ $label }}</legend>
    <textarea class="textarea w-full textarea-bordered h-28 text-sm transition-all duration-200
        focus:textarea-primary focus:shadow-sm
        @error($id) textarea-error @enderror"
              placeholder="{{$placeholder}}" wire:model="{{ $id }}"></textarea>
    @error($id)
    <p class="text-error text-xs mt-1 flex items-center gap-1">
        <span class="material-icons text-xs">error</span>
        {{ $message }}
    </p>
    @enderror
</fieldset>
