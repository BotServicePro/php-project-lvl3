<?php

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
})->name('main');

Route::get('/urls', function () {
    // список всех линков
    $urlsData = DB::table('urls')->orderBy('id', 'desc')->get();

    $checksData = DB::table('url_checks')
        ->distinct('url_id')
        ->orderBy('url_id')
        ->orderBy('created_at', 'desc')
        ->get();
    $checksStatuses = $checksData->keyBy('url_id');

    // ОТЛОВИТЬ ОШИБКУ НЕСУЩЕСТВУЮЩЕГО ИД линка

    $params = ['urlsData' => $urlsData, 'errors' => [], 'messages' => [], 'checksStatuses' => $checksStatuses];
    return view('allUrls', $params);
})->name('allUrls');

Route::post('/urls', function (Request $request) {
    // пост запрос на добавление урлов
    $url = strtolower($request->input('url')['name']); // так же получили урл из запроса и привели к нижнему регистру
    $parsedUrl = parse_url($url); // распарсили урл из запроса
    $rules = [ // создаем правила для валидации
        // bail = при первой же ошибке остановить проверку
        // required = требование на необходимость наличия урла
        // url = распашивается урл и на корректность
        // првоерка на длину урла
        // проверка на уникальность линка, проверка в базе urls в столбе name
        'url.name' => 'bail|required|url|max:100|unique:urls,name'
    ];
    $request->session()->token(); // token
    csrf_token(); // token

    $validator = Validator::make($request->all(), $rules); // валидируем входные данные
    $errorMessage = $validator->errors()->first('url.name'); // получаем сообщения об ошибке
    // заменяем '.name' на пусто, хз как убрать по другому
    $errorMessage = str_replace('.name', '', $errorMessage);
    if ($validator->fails()) { // если есть ошибки, делаем редирект на главную страницу и во влэш пишем ошибку
        if ($errorMessage === 'The url has already been taken.') {
            $id = DB::table('urls')
                ->where('name', "{$parsedUrl['scheme']}://{$parsedUrl['host']}")
                ->first()->id;
            return redirect()->route('singleUrl', ['id' => $id])->withErrors(flash($errorMessage)->warning());
        }
        return redirect('/')->withErrors(flash($errorMessage)->error());
    }

    $valideUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";

    DB::table('urls')->insertGetId( // записываем в бд новый линк
        ['name' => $valideUrl, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
    );

    // получаем ИД только что добавленного линка
    $addedUrlID = DB::table('urls')
        ->where('name', $valideUrl)->first()->id;

    //$params = ['messages' => flash('Url was added!')->success()];
    flash('Url was added!')
        ->success(); // добавляем сообщение
    // редирект на именованную страницу с переданным параметром
    return redirect()
        ->route('singleUrl', ['id' => $addedUrlID]);
})->name('urls.store');

Route::get('/url/{id}', function ($id) {
    // получаем инфу о линке из бд
    $urlData = DB::table('urls')->where('id', $id)->first();

    // делаем выборку проверок по одному линку c сортировкой по времени
    $checksData = DB::table('url_checks')
        ->where('url_id', '=', $id)
        ->orderBy('updated_at', 'desc')
        ->get();
    $params = ['urlData' => $urlData, 'messages' => [], 'checksData' => $checksData];
    return view('singleUrl', $params);
})->name('singleUrl');


Route::post('url/{id}/checks', function ($id) {
    //$response = Http::get('http://mobbit.info')->body(); // получаем html страницу, код
    //$response = Http::get('http://mobbit.info');

    // получаем ссылку по которой идет проверка
    $url = DB::table('urls')->where('id', '=', $id)->first()->name;
    $client = new GuzzleHttp\Client();
    try {
        $client->request('GET', $url);
        $statusCode = $client->request('GET', $url)->getStatusCode(); // получаем статус
    } catch (ConnectException $e) {
        flash("ConnectException: {$e->getMessage()}")->error(); // добавляем сообщение
        return redirect()->route('singleUrl', ['id' => $id]);
    } catch (ClientException $e) {
        $statusCode = $e->getResponse()->getStatusCode();
    } catch (RequestException $e) {
        flash("RequestException: {$e->getMessage()}")->error(); // добавляем сообщение
        return redirect()->route('singleUrl', ['id' => $id]);
    }

    DB::table('url_checks')->insert( // добавляем в таблицу запись
        ['url_id' => $id, 'status_code' => $statusCode, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
    );
    DB::table('urls')
        ->where('id', $id)
        ->update(['updated_at' => Carbon::now()]);
    flash('Url was checked')->success(); // добавляем сообщение
    // Добавить так же проверку на существование домена
    // ДОПИСАТЬ ВСТАВКУ КОРРЕКТНОГО СООБЩЕНИЯ ОБ ОБНОВЛЕНИИ ССЫЛКИ
    return redirect()->route('singleUrl', ['id' => $id]);
})->name('checkUrl');
