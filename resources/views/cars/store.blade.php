{{-- filepath: c:\laragon\www\WheelyGoodCars\resources\views\cars\store.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <br>

    <form method="GET" action="{{ route('cars.index') }}">
        {{-- Tag filter --}}
        <div class="mb-4">
            <button class="btn btn-outline-primary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterTagsCollapse" aria-expanded="false" aria-controls="filterTagsCollapse">
                Filter op tags
            </button>
            <div class="collapse" id="filterTagsCollapse">
                <div class="card card-body mb-2">
                    @foreach($tags as $tag)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="tags[]"
                                   id="filter_tag{{ $tag->id }}"
                                   value="{{ $tag->id }}"
                                   {{ in_array($tag->id, (array)request('tags', [])) ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="form-check-label" for="filter_tag{{ $tag->id }}" style="color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Aantal per pagina --}}
        <div class="mb-2 d-flex align-items-center gap-2">
            <label for="per_page_top" class="form-label mb-0">Aantal auto's per pagina:</label>
            <select name="per_page" id="per_page_top" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Allemaal</option>
            </select>
        </div>

        {{-- Zoekbalk --}}
        <div class="mb-3 d-flex align-items-center gap-2">
            <input type="text" name="search" class="form-control w-auto" placeholder="Zoek op merk of model..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Zoeken</button>
        </div>
    </form>

    {{-- Grid met speelse uitgelichte auto's --}}
    @php
        // Ongeveer 1 op 15 auto's uitlichten, minimaal 1
        $highlightCount = max(1, floor($cars->count() / 15));
        $highlightIndexes = $cars->count() > 0
            ? collect($cars->keys())->shuffle()->take($highlightCount)->toArray()
            : [];
    @endphp
    <div class="car-grid">
        @foreach($cars as $i => $car)
            <div class="car-card {{ in_array($i, $highlightIndexes) ? 'featured-car' : '' }}">
                @if($car->image)
                    <img src="{{ asset('storage/' . $car->image) }}" class="car-card-img" alt="Foto van {{ $car->brand }} {{ $car->model }}">
                @else
                    <div class="car-card-img d-flex align-items-center justify-content-center text-muted" style="height:180px;">Geen foto</div>
                @endif
                <div class="car-card-body">
                    <h5 class="card-title mb-1">{{ $car->brand }} {{ $car->model }}</h5>
                    <div class="mb-1"><span class="badge bg-secondary">{{ $car->license_plate }}</span></div>
                    <div class="mb-1"><strong>Kleur:</strong> {{ $car->color }}</div>
                    <div class="mb-1"><strong>Prijs:</strong> €{{ number_format($car->price, 2) }}</div>
                    <div class="mb-1"><strong>Bouwjaar:</strong> {{ $car->production_year }}</div>
                    <div class="mb-1"><strong>Kilometerstand:</strong> {{ $car->mileage }} km</div>
                    <div class="mb-1">
                        <strong>Tags:</strong>
                        @forelse($car->tags as $tag)
                            <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                        @empty
                            <span class="text-muted">Geen tags</span>
                        @endforelse
                    </div>
                    <a href="{{ route('cars.show', $car->id) }}" class="btn btn-primary btn-sm mt-2">Bekijk</a>
                    @if (Auth::id() === $car->user_id)
                        <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-warning btn-sm mt-2">Bewerk</a>
                        <form action="{{ route('cars.destroy', $car->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">Verwijder</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Aantal weergegeven auto's --}}
    <div class="mt-2 text-muted">
        Weergegeven: {{ $cars->firstItem() ?? 0 }} - {{ $cars->lastItem() ?? 0 }} van {{ $cars->total() }} auto's
    </div>

    {{-- Paginatie --}}
    {{ $cars->withQueryString()->links('pagination::simple-bootstrap-5') }}
</div>
@endsection