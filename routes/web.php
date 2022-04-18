<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;


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


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/', function () {
    return redirect('dashboard');
});

Route::middleware(['auth'])->group(
    function () {
        Route::resource('forms', FormController::class);

        Route::get(
            '/forms/{forms}/fill',
            [FormController::class, 'fill']
        )->name('forms.fill')->withoutMiddleware('auth');
        Route::post(
            '/response',
            [FormController::class, 'response']
        )->name('forms.response')->withoutMiddleware('auth');
        // Route::POST(
        //     '/forms/response',
        //     [FormController::class, 'response']
        // )->name('forms.response');

    }
);

require __DIR__ . '/auth.php';
