<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreateController extends Controller
{
    // INDEX - Alle beschikbare auto's
    public function index(Request $request)
    {
        $tags = Tag::all();
        $carsQuery = Car::where('status', 'available');

        // Zoekfilter
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $carsQuery->where(fn($q) => $q->where('brand', 'like', $search)
                                          ->orWhere('model', 'like', $search));
        }

        // Tags filter
        if ($request->has('tags')) {
            foreach ($request->input('tags') as $tagId) {
                $carsQuery->whereHas('tags', fn($q) => $q->where('tags.id', $tagId));
            }
        }

        $perPage = $request->input('per_page', 20) === 'all' ? 10000 : (int)$request->input('per_page', 20);

        $cars = $carsQuery->with('tags')->orderByDesc('created_at')->paginate($perPage)->appends($request->query());

        return view('cars.store', compact('cars', 'tags', 'perPage'));
    }

    // CREATE - Formulier
    public function create()
    {
        $tags = Tag::all();
        return view('cars.create', compact('tags'));
    }

    // STORE - Auto opslaan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'doors' => 'required|integer|min:1',
            'weight' => 'required|integer|min:0',
            'production_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:255',
            'mileage' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0|max:999999999',
            'license_plate' => 'required|string|regex:/^[A-Z0-9]{1,8}$/i',
            'photo' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['license_plate'] = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $validated['license_plate']));
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'available';

        if ($request->hasFile('photo')) {
            $validated['image'] = $request->file('photo')->store('car_photos', 'public');
        }

        $car = Car::create($validated);

        if (!empty($validated['tags'])) {
            $car->tags()->sync($validated['tags']);
        }

        return redirect()->route('cars.index')->with('success', 'Auto succesvol toegevoegd!');
    }

    // SHOW - Auto details
    public function show(Car $car)
    {
        $car->increment('views');
        return view('cars.show', compact('car'));
    }

    // MINE - Eigen auto's
    public function mine()
    {
        $cars = Car::where('user_id', Auth::id())->get();
        return view('cars.mine', compact('cars'));
    }

    // EDIT - Auto bewerken
    public function edit(Car $car)
    {
        if ($car->user_id !== Auth::id()) {
            abort(403); // Alleen eigenaar mag bewerken
        }

        $tags = Tag::all();
        return view('cars.edit', compact('car', 'tags'));
    }

    // UPDATE - Auto bijwerken
    public function update(Request $request, Car $car)
    {
        if ($car->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'doors' => 'required|integer|min:1',
            'weight' => 'required|integer|min:0',
            'production_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:255',
            'mileage' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0|max:999999999',
            'license_plate' => 'required|string|regex:/^[A-Z0-9]{1,8}$/i',
            'status' => 'required|in:available,sold',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['license_plate'] = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $validated['license_plate']));
        $car->update($validated);
        $car->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('cars.index')->with('success', 'Auto succesvol bijgewerkt!');
    }

    // DESTROY - Auto verwijderen
    public function destroy(Car $car)
    {
        if ($car->user_id !== Auth::id()) {
            abort(403);
        }

        $car->tags()->detach();
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }
        $car->delete();

        return redirect()->route('cars.mine')->with('success', 'Auto succesvol verwijderd!');
    }
}
