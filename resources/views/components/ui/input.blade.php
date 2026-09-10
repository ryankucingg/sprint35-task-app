@props(['id', 'placeholder', 'message','input_type', 'icon', 'disabled', 'hidden'])

<fieldset class="fieldset mb-3 @if(isset($hidden)) hidden @endif">
    <legend class="fieldset-legend text-xs font-semibold text-base-content/70 uppercase tracking-wide">{{ $placeholder }}</legend>
    <label class="input input-bordered flex items-center gap-3 w-full transition-all duration-200
        focus-within:input-primary focus-within:shadow-sm
        @error($id) input-error @enderror
        @if(isset($disabled) && $disabled == 'true') opacity-60 bg-base-200 @endif">
        @if($icon != null)
            <span class="material-symbols-outlined text-base-content/40 text-lg">{{$icon}}</span>
        @endif
        <input type="{{ $input_type }}" class="grow text-sm" placeholder="..." wire:model="{{ $id }}"
               @if(isset($disabled) && $disabled == "true") disabled @endif/>
    </label>
    @error($id)
    <p class="text-error text-xs mt-1 flex items-center gap-1">
        <span class="material-icons text-xs">error</span>
        {{ $message }}
    </p>
    @enderror
</fieldset>
