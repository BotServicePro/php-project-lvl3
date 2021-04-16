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
})->name('main');


function getHostFromUrl($url) { // функция получения только домена из линка
    $parsedUrl = parse_url($url);
    if (isset($parsedUrl['host'])) {
        return $parsedUrl['host'];
    }
    echo 'ERROR FROM FUNCTION getHostFromUrl';
}

function getSchemeFromUrl($url) { // функция получения только протокола из линка
    $parsedUrl = parse_url($url);
    if (isset($parsedUrl['scheme'])) {
        return $parsedUrl['scheme'];
    }
    echo 'ERROR FROM FUNCTION getSchemeFromUrl';
}


Route::post('/', function (Request $request) { // пост запрос на добавление линков
    $url = $request['url']['name']; // получили линк из запроса
    $parsedUrl = parse_url($url); // распарсили линк из запроса
    $schemeFromRequestedUrl = getSchemeFromUrl($url); // получили протокол из запроса линка
    $hostFromRequestedUrl = getHostFromUrl($url); // получили хост линка из запроса

    if (!isset($schemeFromRequestedUrl) || !isset($hostFromRequestedUrl)) { // проверка, если нет протокола или самого тела линка
        $params = ['messages' => flash('Incorrect url!')->error(), 'url' => $url];
        return redirect()->route('main', $params);
    }

    $allLinksFromDB = DB::table('urls')->get(); // получили все данные из таблицы urls

    foreach ($allLinksFromDB as $linkFromDB) { // перебираем все строки бд с доступом к полю name
        $onlyHostFromDB = getHostFromUrl($linkFromDB->name); // получили хост перебираемого линка из бд
        if ($onlyHostFromDB === $hostFromRequestedUrl) { // если хост перебираемого линка из бд совпал с хостом из запроса
            $params = ['messages' => flash('Url is already exists!')->warning()];
            return view('main', $params);
        }
    }

    DB::table('urls')->insertGetId( // записываем в бд новый линк
        ['name' => $url, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
    );

    $params = ['messages' => flash('Url was added!')->success()];
    return view('main', $params);
})->name('postUrl');


Route::get('/urls/{id}', function (Request $request) { // инфа о единичном урле c редактированием и так далее
    $params = ['url' => [], 'messages' => []];
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

