@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Mijn aanbod</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($cars->isEmpty())
        <p>Je hebt nog geen auto's toegevoegd.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Kenteken</th>
                    <th>Status</th>
                    <th>Prijs</th>
                    <th>Merk</th>
                    <th>Model</th>
                    <th>Weergaven</th>
                    <th>Tags</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cars as $car)
                    <tr>
                        <td>
                            @if($car->image)
                                <img src="{{ asset('storage/' . $car->image) }}" alt="Foto" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $car->license_plate }}</td>
                        <td>{{ $car->status === 'available' ? 'Beschikbaar' : 'Verkocht' }}</td>
                        <td>€{{ number_format($car->price, 2) }}</td>
                        <td>{{ $car->brand }}</td>
                        <td>{{ $car->model }}</td>
                        <td>{{ $car->views }}</td>
                        <td>
                            @forelse($car->tags as $tag)
                                <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                            @empty
                                <span class="text-muted">Geen tags</span>
                            @endforelse
                        </td>
                        <td>
                            <a href="{{ route('cars.show', $car->id) }}" class="btn btn-info btn-sm">Bekijk</a>
                            <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-warning btn-sm">Bewerk</a>
                            <form action="{{ route('cars.destroy', $car->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">Verwijder</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection