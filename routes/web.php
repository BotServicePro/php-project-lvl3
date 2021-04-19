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

Route::get('/', function (Request $request) { // главная страница
    $params = ['url' => [], 'errors' => [], 'messages' => []];
    return view('main', $params);
})->name('main');

//
//Route::get('/urls/{id}', function ($id) { // инфа о единичном урле c редактированием и так далее
//    echo 'Sinle url page';
//    exit;
//    $params = ['url' => [], 'messages' => []];
//    return view('main', $params);
//})->name('singleUrl');
//
//Route::post('/urls/{id}/edit', function (Request $request) { // пост запрос на редактирование урла
//    $params = ['url' => [], 'errors' => [], 'messages' => []];
//    return view('main', $params);
//})->name('editUrl');
//
//
//Route::get('/urls', function (Request $request) { // список всех линков
//    $params = ['url' => [], 'errors' => [], 'messages' => []];
//    return view('main', $params);
//});


Route::post('/', function (Request $request) { // пост запрос на добавление урлов
    $url = $request->input('url')['name']; // так же получили урл из запроса
    $parsedUrl = parse_url($url); // распарсили урл из запроса
    $rules = [ // создаем правила для валидации
        // bail = при первой же ошибке остановить проверку
        // required = требование на необходимость наличия урла
        // url = распашивается урл и на корректность
        // првоерка на длину урла
        // проверка на уникальность линка, проверка в базе urls в столбе name
        'url.name' => 'bail|required|url|max:100|unique:urls,name'
    ];

    $validator = Validator::make($request->all(), $rules); // валидируем входные данные
    $errorMessage = $validator->errors()->first('url.name'); // получаем сообщения об ошибке
    $errorMessage = str_replace('.name',  '', $errorMessage); // заменяем '.name' на пусто, хз как убрать по другому

    if ($validator->fails()) { // если есть ошибки, делаем редирект на главную страницу и во влэш пишем ошибку
        return redirect('/')
            ->withErrors(flash($errorMessage)->error());
    }

    $valideUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";

    DB::table('urls')->insertGetId( // записываем в бд новый линк
        ['name' => $valideUrl, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
    );

    // получаем ИД только что добавленного линка
    $addedUrlID = DB::table('urls')->where('name', $valideUrl)->first()->id;

    $params = ['messages' => flash('Url was added!')->success()];
    return view('main', $params);
    //return route('singleUrl', ['id' => $addedUrlID]);
})->name('urls.store');


