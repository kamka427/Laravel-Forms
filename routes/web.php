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


// Route::get('/dashboard', function () {
//     return view('');
// })->middleware(['auth'])->name('dashboard');

Route::get('/', function () {
    return redirect('forms.home');
});

Route::middleware(['auth'])->group(
    function () {
        Route::resource('forms', FormController::class);

        Route::get('/', [FormController::class, 'home'])->name('forms.home');
        Route::get(
            '/forms/{forms}/fill',
            [FormController::class, 'fill']
        )->name('forms.fill')->withoutMiddleware('auth');
        Route::post(
            '/response/{form}',
            [FormController::class, 'response']
        )->name('forms.response')->withoutMiddleware('auth');
        Route::delete(
            '/forms/{form}/delete',
            [FormController::class, 'destroy']
        )->name('forms.destroy');
        Route::patch('/forms/{form}/restore', [FormController::class, 'restore'])->name('forms.restore');
    }
);

require __DIR__ . '/auth.php';
