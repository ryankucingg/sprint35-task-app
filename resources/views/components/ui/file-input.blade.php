@props(['id', 'placeholder', 'message'])


<fieldset class="fieldset mb-2 w-full max-w-xs">
    <legend class="fieldset-legend">{{ $placeholder }}</legend>
    <input type="file" class="file-input file-input-bordered w-full max-w-xs" wire:model="{{ $id }}"/>
    @error($id)
    <label class="label">
        <span class="text-error text-sm">{{ $message }}</span>
    </label>
    @enderror
</fieldset>
