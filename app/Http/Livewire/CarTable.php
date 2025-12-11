<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Car;
use App\Models\Tag;

class CarTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 20;
    public $selectedTags = [];

    protected $queryString = ['search', 'perPage', 'selectedTags', 'page'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatingSelectedTags() { $this->resetPage(); }

    public function render()
    {
        $tags = Tag::all();

        $cars = Car::query()
            ->where('status', 'available')
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('brand', 'like', $search)
                      ->orWhere('model', 'like', $search);
                });
            })
            ->when($this->selectedTags, function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('tags.id', $this->selectedTags);
                });
            })
            ->with('tags')
            ->orderByDesc('created_at')
            ->paginate($this->perPage === 'all' ? 10000 : $this->perPage);

        return view('livewire.car-table', compact('cars', 'tags'));
    }
}