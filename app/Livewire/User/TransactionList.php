<?php

namespace App\Livewire\User;

use App\Models\Transaction;
use Livewire\Component;

class TransactionList extends Component
{

    public $txList = ''; 
    public function mount(){
        $this->txList = Transaction::where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC')->get();
    }

    public function render()
    {
        return view('livewire.user.transaction-list');
    }
}
