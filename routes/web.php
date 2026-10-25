<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (app()->environment('local', 'testing')) {
    Route::get('/_ui', function () {
        return view('ui-catalog');
    })->name('ui.catalog');
}
