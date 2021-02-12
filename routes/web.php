<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    Route::post('user/book/add-to-my-read-list', [App\Http\Controllers\BookToReadController::class, 'store'])->name('book.store')->middleware('auth');
    
    Route::get('/user/my-read-list', [App\Http\Controllers\BookToReadController::class, 'index'])->name('book.readList');
    
    Route::get('/user/my-read-list-data', [App\Http\Controllers\BookToReadController::class, 'getMyReadListDAta'])->name('book.readListData');
    
    Route::get('/user/view-book/{bookToRead}', [App\Http\Controllers\BookToReadController::class, 'show'])->name('book.show');
    
    Route::delete('/user/remove-book/{bookToRead}', [App\Http\Controllers\BookToReadController::class, 'destroy'])->name('book.destroy');


});


