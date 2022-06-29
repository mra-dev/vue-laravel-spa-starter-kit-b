<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('session', function () {
//    return session()->all();
//});

Route::get('/', function () {
    return [
        'JaryanHub' => '1.0.0',
        'Laravel' => app()->version()
    ];
});

Route::get('/home', function () {
    return 'Welcome Home !';
//    return view('test');
});

require __DIR__.'/auth.php';
