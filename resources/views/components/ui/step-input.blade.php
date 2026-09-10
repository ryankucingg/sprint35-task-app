@props(['id', 'placeholder', 'message','step', 'icon','min','max'])

<fieldset class="fieldset mb-2">
    <legend class="fieldset-legend">{{ $placeholder }}</legend>
    <label class="input input-bordered flex items-center gap-2 w-full
                @error($id)
        input-error
@enderror">
        @if($icon != null)
            <span class="material-icons">{{$icon}}</span>
        @endif
        <input type="number" step="{{$step}}" @if(isset($min)) min="{{ $min }}" @endif @if(isset($max)) max="{{ $max }}" @endif class="grow" placeholder="..." wire:model="{{ $id }}"/>
    </label>
    @error($id)
    <label class="label">
        <span class="text-error text-sm">{{ $message }}</span>
    </label>
    @enderror
</fieldset>
