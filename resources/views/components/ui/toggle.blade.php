@props(['id', 'label'])

<fieldset class="fieldset mb-3">
    <label class="label cursor-pointer justify-start gap-3 p-3 rounded-lg border border-base-content/10 hover:bg-base-200/50 transition-colors duration-200">
        <input type="checkbox" class="toggle toggle-primary toggle-sm" wire:model="{{ $id }}" />
        <span class="text-base-content text-sm font-medium">{{ $label }}</span>
    </label>
</fieldset>
