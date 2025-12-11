<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class SearchCars extends Component
{
    public $search = '';

    public function mount()
    {
        $this->search = '';
    }

    public function search()
    {
        $this->emit('searchCars', $this->search);
    }

    public function render()
    {
        return view('livewire.search-cars');
    }
}