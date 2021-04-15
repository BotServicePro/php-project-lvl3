<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

Route::get('/', function (Request $request) {

    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
});

Route::post('/postUrl', function (Request $request) {
    $url = $request['url']['name'];
    $errors = '';
    $params = ['messages' => flash('Url was added!')->success(), 'errors' => []];
    return view('main', $params);
})->name('postUrl');


