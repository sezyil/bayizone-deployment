{{-- input error --}}
@props(['for'])
<div>
    @error($for)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
