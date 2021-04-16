<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

Route::get('/', function (Request $request) { // главная страница
    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
});

Route::post('/postUrl', function (Request $request) { // пост запрос на добавление линков
    $url = $request['url']['name'];
    $errors = '';

    DB::table('urls')->insertGetId(
        ['name' => $url, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
    );


    $params = ['messages' => flash('Url was added!')->success(), 'errors' => []];
    return view('main', $params);
})->name('postUrl');


Route::get('/urls/{id}', function (Request $request) { // инфа о единичном урле c редактированием и так далее
    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
})->name('singleUrl');

Route::post('/urls/{id}/edit', function (Request $request) { // пост запрос на редактирование урла
    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
})->name('editUrl');


Route::get('/urls', function (Request $request) { // список всех линков
    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
});

