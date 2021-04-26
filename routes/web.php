<?php

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
    // главная страница
    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
})->name('main');

Route::get('/urls', function () {
    // список всех линков
    $urlsData = DB::table('urls')->get(); // через ПЛЮК получаем все значения одного столбца
    $params = ['urlsData' => $urlsData, 'errors' => [], 'messages' => []];
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
            $id = DB::table('urls')->where('name', "{$parsedUrl['scheme']}://{$parsedUrl['host']}")
                ->first()->id;
            return redirect()->route('singleUrl', ['id' => $id])
                ->withErrors(flash($errorMessage)->warning());
        }
        return redirect('/')
            ->withErrors(flash($errorMessage)->error());
    }

    $valideUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";

    DB::table('urls')->insertGetId( // записываем в бд новый линк
        ['name' => $valideUrl, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
    );

    // получаем ИД только что добавленного линка
    $addedUrlID = DB::table('urls')->where('name', $valideUrl)->first()->id;

    //$params = ['messages' => flash('Url was added!')->success()];
    flash('Url was added!')->success(); // добавляем сообщение
    // редирект на именованную страницу с переданным параметром
    return redirect()->route('singleUrl', ['id' => $addedUrlID]);
})->name('urls.store');

Route::get('/url/{id}', function ($id) {
    // получаем инфу о линке из бд
    $urlData = DB::table('urls')->where('id', $id)->first();

    // делаем выборку проверок по одному линку
    $checksData = DB::table('url_checks')->where('url_id', '=', $id)->get();
    dump($checksData);
    $params = ['urlData' => $urlData, 'messages' => [], 'checksData' => $checksData];
    return view('singleUrl', $params);
})->name('singleUrl');


Route::post('url/{id}/checks', function ($id) {
    DB::table('url_checks')->insert( // добавляем в таблицу запись
        ['url_id' => $id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
    );
    flash('Url was checked')->success(); // добавляем сообщение
    // Добавить так же проверку на существование домена
    // ДОПИСАТЬ ВСТАВКУ КОРРЕКТНОГО СООБЩЕНИЯ ОБ ОБНОВЛЕНИИ ССЫЛКИ
    return redirect()->route('singleUrl', ['id' => $id]);
})->name('checkUrl');
