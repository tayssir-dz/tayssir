<?php

use App\Mail\welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('home');
    $user = \App\Models\User::find(1);
    $division = \App\Models\Division::find(1);
    $materials = $division->materials()->get();


    dd([
        "user" => $user->wilaya->name,
        "division" => [
            "name" => $division->name,
            "materials" => $materials->pluck("id")->toArray(),
            "user_progress" => $materials->map(function ($material) use ($user) {
                return [
                    "material_id" => $material->id,
                    "progress" => $user->materialProgress($material)
                ];
            })->toArray()
        ]
    ]);
})->name("login");

Route::get('/testing', function () {
    return view('testing');
})->name('testing');
