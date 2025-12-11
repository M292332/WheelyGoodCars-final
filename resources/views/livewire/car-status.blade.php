<div>
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <label for="status-{{ $carId }}" class="form-label">Status:</label>
        <select wire:model="status" id="status-{{ $carId }}" class="form-control">
            <option value="available">Beschikbaar</option>
            <option value="sold">Verkocht</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="price-{{ $carId }}" class="form-label">Vraagprijs:</label>
        <div class="input-group">
            <span class="input-group-text">€</span>
            <input type="number" step="0.01" wire:model="price" id="price-{{ $carId }}" class="form-control">
        </div>
    </div>

    <button wire:click="updateStatus" class="btn btn-primary">Status en prijs bijwerken</button>
</div>