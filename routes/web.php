<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect('login');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('activities');
    });

    Volt::route('activities', 'pages.activities')->name('activities');
    Volt::route('history', 'pages.history')->name('history');
    Volt::route('members', 'pages.members')->name('members');
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
