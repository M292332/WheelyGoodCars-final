<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Car;
use Illuminate\Support\Facades\Auth;

class CarStatus extends Component
{
    public $carId;
    public $status;
    public $price;

    public function mount($carId)
    {
        $car = Car::findOrFail($carId);
        if ($car->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $this->carId = $carId;
        $this->status = $car->status;
        $this->price = $car->price;
    }

    public function updateStatus()
    {
        $car = Car::findOrFail($this->carId);
        if ($car->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->validate([
            'status' => 'required|in:available,sold',
            'price' => 'required|numeric|min:0',
        ]);

        $car->update([
            'status' => $this->status,
            'price' => $this->price,
        ]);

        session()->flash('success', 'Status en prijs succesvol bijgewerkt!');
    }

    public function render()
    {
        return view('livewire.car-status');
    }
}