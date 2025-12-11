{{-- filepath: resources/views/livewire/search-cars.blade.php --}}
<div class="d-flex mb-3 gap-2">
    <input
        type="text"
        wire:model.defer="search"
        class="form-control w-auto"
        placeholder="Zoek op merk of model..."
    >
    <button wire:click="search" class="btn btn-primary">Zoeken</button>
</div>