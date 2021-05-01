<?php

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DiDom\Document;

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

// проверка на длину и обрезка
//function lengthCheck($data, $offSet, $dataLength): string
//{
//    if (strlen($data) >= $dataLength) {
//        $data = substr($data, $offSet, $dataLength);
//        return "{$data}...";
//    }
//    return $data;
//}

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


    // ДОБАВИТЬ ПРОСТУЮ КАПЧКУ НА ВВОД ЛИНКА


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
//    Проверьте наличие тега <h1> на странице. Если он есть то запишите его содержимое в базу.
//    Проверьте наличие тега <meta name="keywords" content="..."> на странице.
//    Если он есть то запишите содержимое аттрибута content в базу.
//    Проверьте наличие тега <meta name="description" content="..."> на странице.
//    Если он есть то запишите содержимое аттрибута content в базу.
//    Выведите эту информацию в списке проверок конкретного сайта.

    try {
        $document = new Document($url, true);
    } catch (RuntimeException $e) {
        flash("ConnectException: {$e->getMessage()}")->error(); // добавляем сообщение
        return redirect()->route('singleUrl', ['id' => $id]);
    }

    $h1 = '';
    $keywords = '';
    $description = '';
    $offSet = 0;
    $dataLength = 50;

    try {
        $h1 = $document->find('h1')[0]->text();
    } catch (ErrorException $e) {
    }

    try {
        $keywords = $document->find('*[name=keywords]')[0]->content;
    } catch (ErrorException $e) {
        if (isset($document->find('*[name=Keywords]')[0]->content)) {
            $keywords = $document->find('*[name=Keywords]')[0]->content;
        }
        if (isset($document->find('*[property=og:keywords]')[0]->content)) {
            $keywords = $document->find('*[property=og:keywords]')[0]->content;
        }
    }

    try {
        $description = $document->find('*[name=Description]')[0]->content;
    } catch (ErrorException $e) {
        if (isset($document->find('*[name=description]')[0]->content)) {
            $description = $document->find('*[name=description]')[0]->content;
        }
        if (isset($document->find('*[property=og:description]')[0]->content)) {
            $description = $document->find('*[property=og:description]')[0]->content;
        }
    }
    if (strlen($description) >= $dataLength) {
        $data = substr($description, $offSet, $dataLength);
        $description = "{$data}...";
    }

    if (strlen($keywords) >= $dataLength) {
        $data = substr($keywords, $offSet, $dataLength);
        $keywords = "{$data}...";
    }
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
        [
            'url_id' => $id,
            'status_code' => $statusCode,
            'h1' => $h1,
            'keywords' => $keywords,
            'description' => $description,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]
    );
    DB::table('urls')
        ->where('id', $id)
        ->update(['updated_at' => Carbon::now()]);
    flash('Url was checked')->message(); // добавляем сообщение
    return redirect()->route('singleUrl', ['id' => $id]);
})->name('checkUrl');
