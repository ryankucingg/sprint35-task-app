@props(['id', 'data', 'value','option', 'label', 'message'])

<fieldset class="fieldset mb-3">
    <legend class="fieldset-legend text-xs font-semibold text-base-content/70 uppercase tracking-wide">{{ $label }}</legend>
    <div class="p-4 border border-base-content/15 rounded-lg bg-base-200/30 @error($id) border-error @enderror">
        <div class="flex flex-wrap gap-2">
            @foreach($data as $d)
                <input class="btn btn-sm transition-all duration-200" type="checkbox" value="{{ $d[$value] }}" wire:model="{{ $id }}" name="options" aria-label="{{ $d[$option] }}"/>
            @endforeach
        </div>
    </div>
    @error($id)
    <p class="text-error text-xs mt-1 flex items-center gap-1">
        <span class="material-icons text-xs">error</span>
        {{ $message }}
    </p>
    @enderror
</fieldset>
