@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Zoek RDW Gegevens</h2>

    <form method="GET" action="{{ route('cars.rdw') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="kenteken" value="{{ $licensePlate ?? '' }}" placeholder="Kenteken (bijv. 1XXD48)" class="form-control">
            <button type="submit" class="btn btn-primary">Zoek</button>
        </div>
    </form>

    @if ($errors->has('error'))
        <div class="alert alert-danger">
            {{ $errors->first('error') }}
        </div>
    @endif

    @if (!empty($carData) && !isset($carData['error']))
        <h3>RDW Gegevens:</h3>
        <pre>{{ print_r($carData, true) }}</pre>
    @else
        <p>Geen gegevens beschikbaar.</p>
    @endif
</div>
@endsection