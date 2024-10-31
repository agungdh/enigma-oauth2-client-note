<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Middleware\LoggedIn;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\NoteController;

Route::get('/', function () {
    if (session('access_token')) {
        return redirect('/home');
    }

    return view('welcome');
});
Route::middleware([LoggedIn::class])->group(function() {
    Route::get('/home', function () {
        if (!session('access_token')) {
            return redirect('/');
        }
    
        return view('logged-in');
    });

    Route::prefix('/note')->group(function() {
        Route::get('/', [NoteController::class, 'index']);
        Route::post('/', [NoteController::class, 'store']);
        Route::delete('/{note}', [NoteController::class, 'destroy']);
    });
});

require __DIR__.'/auth.php';