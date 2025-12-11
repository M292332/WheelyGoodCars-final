{{-- filepath: resources/views/livewire/car-table.blade.php --}}
<div>
    <h2>Alle auto's</h2>

    {{-- Dropdown boven de tabel --}}
    <div class="mb-2 d-flex align-items-center gap-2">
        <label for="per_page_top" class="form-label mb-0">Aantal per pagina:</label>
        <select wire:model="perPage" id="per_page_top" class="form-select d-inline-block w-auto">
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">Allemaal</option>
        </select>
    </div>

    {{-- Tag filter --}}
    <div class="mb-4">
        <button class="btn btn-outline-primary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterTagsCollapse" aria-expanded="false" aria-controls="filterTagsCollapse">
            Filter op tags
        </button>
        <div class="collapse" id="filterTagsCollapse">
            <div class="card card-body mb-2">
                @foreach($tags as $tag)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" wire:model="selectedTags" id="filter_tag{{ $tag->id }}" value="{{ $tag->id }}">
                        <label class="form-check-label" for="filter_tag{{ $tag->id }}" style="color: {{ $tag->color }}">
                            {{ $tag->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Zoekbalk --}}
    <div class="mb-3 d-flex align-items-center gap-2">
        <input type="text" wire:model.debounce.500ms="search" class="form-control w-auto" placeholder="Zoek op merk of model...">
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Merk</th>
                <th>Model</th>
                <th>Kenteken</th>
                <th>Zitplaatsen</th>
                <th>Deuren</th>
                <th>Gewicht</th>
                <th>Bouwjaar</th>
                <th>Kleur</th>
                <th>Kilometerstand</th>
                <th>Tags</th>
                <th>Prijs</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cars as $car)
                <tr>
                    <td>
                        @if($car->image)
                            <img src="{{ asset('storage/' . $car->image) }}" alt="Foto" style="max-width: 60px; max-height: 40px; object-fit: cover;">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $car->brand }}</td>
                    <td>{{ $car->model }}</td>
                    <td>{{ $car->license_plate }}</td>
                    <td>{{ $car->seats }}</td>
                    <td>{{ $car->doors }}</td>
                    <td>{{ $car->weight }} kg</td>
                    <td>{{ $car->production_year }}</td>
                    <td>{{ $car->color }}</td>
                    <td>{{ $car->mileage }} km</td>
                    <td>
                        @forelse($car->tags as $tag)
                            <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                        @empty
                            <span class="text-muted">Geen tags</span>
                        @endforelse
                    </td>
                    <td>€{{ number_format($car->price, 2) }}</td>
                    <td>
                        <a href="{{ route('cars.show', $car->id) }}" class="btn btn-info btn-sm">Bekijk</a>
                        @if (auth()->id() === $car->user_id)
                            <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-warning btn-sm">Bewerk</a>
                            <form action="{{ route('cars.destroy', $car->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">Verwijder</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="13">Geen auto's beschikbaar.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginatie --}}
    @if($perPage !== 'all')
        {{ $cars->links('pagination::simple-bootstrap-5') }}
    @endif
</div>