@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Auto bewerken</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cars.update', $car->id) }}" method="POST" id="editCarForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="license_plate" class="form-label">Kenteken:</label>
            <div class="license-plate-wrapper">
                <span class="license-plate-nl">NL</span>
                <input type="text"
                    class="form-control license-plate-input @error('license_plate') is-invalid @enderror"
                    id="license_plate"
                    name="license_plate"
                    value="{{ old('license_plate', $car->license_plate) }}"
                    placeholder="vul uw kenteken in"
                    required maxlength="8"
                    style="text-transform:uppercase"
                    autocomplete="off">
            </div>
            @error('license_plate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Vul het kenteken in om auto-informatie automatisch te laden (gebruik geen streepjes). Dit kan even duren.</small>
            <div id="rdw-loading" class="form-text text-info" style="display:none;">RDW gegevens ophalen...</div>
            <div id="rdw-error" class="form-text text-danger" style="display:none;"></div>
        </div>

        <div class="mb-3">
            <label for="brand" class="form-label">Merk:</label>
            <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand', $car->brand) }}" required>
        </div>

        <div class="mb-3">
            <label for="model" class="form-label">Model:</label>
            <input type="text" class="form-control" id="model" name="model" value="{{ old('model', $car->model) }}" required>
        </div>

        <div class="mb-3">
            <label for="seats" class="form-label">Zitplaatsen:</label>
            <input type="number" class="form-control" id="seats" name="seats" value="{{ old('seats', $car->seats) }}" required>
        </div>

        <div class="mb-3">
            <label for="doors" class="form-label">Aantal deuren:</label>
            <input type="number" class="form-control" id="doors" name="doors" value="{{ old('doors', $car->doors) }}" required>
        </div>

        <div class="mb-3">
            <label for="weight" class="form-label">Massa rijklaar:</label>
            <input type="number" class="form-control" id="weight" name="weight" value="{{ old('weight', $car->weight) }}" required>
        </div>

        <div class="mb-3">
            <label for="production_year" class="form-label">Jaar van productie:</label>
            <input type="number" class="form-control" id="production_year" name="production_year" value="{{ old('production_year', $car->production_year) }}" required>
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">Kleur:</label>
            <input type="text" class="form-control" id="color" name="color" value="{{ old('color', $car->color) }}" required>
        </div>

        <div class="mb-3">
            <label for="mileage" class="form-label">Kilometerstand:</label>
            <div class="input-group">
                <input type="number" class="form-control" id="mileage" name="mileage" value="{{ old('mileage', $car->mileage) }}" required>
                <span class="input-group-text">km</span>
            </div>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Vraagprijs:</label>
            <div class="input-group">
                <span class="input-group-text">€</span>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $car->price) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="available" {{ old('status', $car->status) == 'available' ? 'selected' : '' }}>Beschikbaar</option>
                <option value="sold" {{ old('status', $car->status) == 'sold' ? 'selected' : '' }}>Verkocht</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tags:</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <div class="form-check" style="min-width: 150px;">
                        <input class="form-check-input" type="checkbox" name="tags[]" id="tag{{ $tag->id }}"
                            value="{{ $tag->id }}"
                            @if(in_array($tag->id, old('tags', $car->tags->pluck('id')->toArray()))) checked @endif>
                        <label class="form-check-label" for="tag{{ $tag->id }}" style="color: {{ $tag->color }}">
                            {{ $tag->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <small class="form-text text-muted">Selecteer één of meerdere tags.</small>
        </div>

        <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
        <a href="{{ route('cars.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
function fetchRdwData(plate) {
    document.getElementById('rdw-loading').style.display = 'block';
    document.getElementById('rdw-error').style.display = 'none';

    fetch('/rdw/' + plate)
        .then(res => res.json())
        .then(data => {
            document.getElementById('rdw-loading').style.display = 'none';
            if (data.error) {
                document.getElementById('rdw-error').textContent = data.error;
                document.getElementById('rdw-error').style.display = 'block';
            } else {
                if(data.brand) document.getElementById('brand').value = data.brand;
                if(data.model) document.getElementById('model').value = data.model;
                if(data.seats) document.getElementById('seats').value = data.seats;
                if(data.doors) document.getElementById('doors').value = data.doors;
                if(data.weight) document.getElementById('weight').value = data.weight;
                if(data.production_year) document.getElementById('production_year').value = data.production_year;
                if(data.color) document.getElementById('color').value = data.color;
            }
        })
        .catch(() => {
            document.getElementById('rdw-loading').style.display = 'none';
            document.getElementById('rdw-error').textContent = 'Kon RDW gegevens niet ophalen.';
            document.getElementById('rdw-error').style.display = 'block';
        });
}

// Automatisch RDW ophalen als kenteken wijzigt
document.getElementById('license_plate').addEventListener('change', function() {
    const plate = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    if(plate.length >= 5) {
        fetchRdwData(plate);
    }
});
</script>
@endsection