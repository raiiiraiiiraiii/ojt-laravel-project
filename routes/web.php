<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TodoDashboard; // Siguraduhing nandito 'to sa taas

Route::get('/', TodoDashboard::class)->name('todo.index');