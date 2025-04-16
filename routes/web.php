<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->get('/', function () {
    return Inertia::render('app');

})->name('/');


require __DIR__.'/auth.php';