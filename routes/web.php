<?php

use App\Mail\welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    // admin user
    $user = \App\Models\User::find(1);

    dd([
        "email" => $user->email,
        "division" => $user->division->name,
        "subscriptions" => $user->subscriptions->pluck("name")->toArray(),
        "materials" => $user->accessibleMaterials,
    ]);
})->name("login");

Route::get('/testing', function () {
    return view('testing');
})->name('testing');
