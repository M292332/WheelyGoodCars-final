@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Auto details</h2>

    @if($car->image)
    <img src="{{ Storage::url($car->image) }}" alt="Foto van {{ $car->brand }} {{ $car->model }}" class="img-fluid mb-3" style="max-width:300px;">
    @endif



    <ul>
        <li><strong>Merk:</strong> {{ $car->brand }}</li>
        <li><strong>Model:</strong> {{ $car->model }}</li>
        <li><strong>Kenteken:</strong> {{ $car->license_plate }}</li>
        <li><strong>Zitplaatsen:</strong> {{ $car->seats }}</li>
        <li><strong>Aantal deuren:</strong> {{ $car->doors }}</li>
        <li><strong>Massa rijklaar:</strong> {{ $car->weight }} kg</li>
        <li><strong>Jaar van productie:</strong> {{ $car->production_year }}</li>
        <li><strong>Kleur:</strong> {{ $car->color }}</li>
        <li><strong>Kilometerstand:</strong> {{ $car->mileage }} km</li>
        <li><strong>Vraagprijs:</strong> €{{ number_format($car->price, 2) }}</li>
    </ul>

    <p class="text-muted mb-2">Aantal keer bekeken: <strong>{{ $car->views }}</strong></p>

    <div class="mb-3">
        <strong>Tags:</strong>
        @forelse($car->tags as $tag)
            <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
        @empty
            <span class="text-muted">Geen tags</span>
        @endforelse
    </div>

    @if (Auth::id() === $car->user_id)
        <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-warning btn-sm">Bewerk</a>
        <form action="{{ route('cars.destroy', $car->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">Verwijder</button>
        </form>
    @endif

    <a href="{{ route('cars.index') }}" class="btn btn-secondary btn-sm">Terug naar alle auto's</a>

    <!-- Bootstrap Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="viewToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header">
                <strong class="me-auto">Notificatie</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Vandaag keken 10 klanten deze {{ $car->brand }} {{ $car->model }} <br> @if($car->image)
                <img src="{{ asset('storage/' . $car->image) }}" alt="Foto van {{ $car->brand }} {{ $car->model }}" class="img-fluid mb-3" style="max-width:300px;">
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Wait 10 seconds after page load to show the toast
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                var toastElement = document.getElementById('viewToast');
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
            }, 10000); // 10 seconds delay
        });
    </script>
@endsection
