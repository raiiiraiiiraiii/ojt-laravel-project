<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Todo Dashboard')]
class TodoDashboard extends Component
{
    public function render()
    {
        return view('livewire.todo-dashboard');
    }
}