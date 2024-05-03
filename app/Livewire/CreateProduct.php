<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateProduct extends Component
{
    
    #[Validate('required|min:5')]
    public $name = '';

    public function save(){
        // dd($this->name);
        Product::create(['name' => $this->name]);
        $this->reset('name');
        session()->flash('success', 'Game Created');
    }

    public function render()
    {
        return view('livewire.create-product');
    }
}
