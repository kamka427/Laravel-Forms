<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormsController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('dashboard');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::resource('forms', FormsController::class);

Route::get('/forms/create', function () {
    return view('forms.create');
})->middleware(['auth'])->name('forms.create');

require __DIR__ . '/auth.php';
