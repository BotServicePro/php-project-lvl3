<?php

use Illuminate\Support\Facades\Http;
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
Route::get('/', function () {
    return view('index');
})->name('main.page');

Route::get('/urls', function () {
    $linksPerPage = 10;
    $urls = DB::table('urls')
        ->orderBy('id', 'asc')
        ->paginate($linksPerPage);
    $lastChecks = DB::table('url_checks')
        ->distinct('url_id')
        ->orderBy('url_id')
        ->orderBy('created_at', 'desc')
        ->get()
        ->keyBy('url_id');
    return view('urls/index', compact('urls', 'lastChecks'));
})->name('urls.index');

Route::post('/urls', function (Request $request) {
    $url = mb_strtolower($request->input('url')['name']);
    $parsedUrl = parse_url($url);
    $rules = [
        'url.name' => 'bail|required|url|max:100'
    ];
    $validator = Validator::make($request->all('url'), $rules);
    $errorMessage = $validator
        ->errors()
        ->first('url.name');
    if ($validator->fails()) {
        flash($errorMessage)->error();
        return redirect(route('main.page'))
            ->withErrors($validator);
    }
    $validatedUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";
    if (DB::table('urls')->where('name', $validatedUrl)->exists()) {
        flash(__('validation.unique'))->warning();
        $id = DB::table('urls')
            ->where('name', $validatedUrl)
            ->first()->id;
    } else {
        flash('Url was added!')->success();
        $id = DB::table('urls')->insertGetId(
            [
                'name' => $validatedUrl,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
    return redirect(route('show.url', ['id' => $id]));
})->name('urls.store');

Route::get('/url/{id}', function ($id) {
    $urlData = DB::table('urls')->find($id);
    abort_unless(DB::table('urls')->where('id', $id)->exists(), 404);
    $checksData = DB::table('url_checks')
        ->where('url_id', '=', $id)
        ->orderBy('updated_at', 'desc')
        ->get();
    return view('urls/show', compact('urlData', 'checksData'));
})->name('show.url');

Route::post('url/{id}/checks', function ($id) {
    $url = DB::table('urls')->find($id)->name;
    $offSet = 0;
    $dataLength = 50;
    try {
        $response = Http::get($url);
    } catch (Exception $e) {
            flash("Exception: {$e->getMessage()}")->error();
            return redirect(route('show.url', ['id' => $id]));
    }
    $document = new Document($response->body());
    $h1 = optional($document->first('h1'))->text();
    $keywords = optional($document->first('meta[name=keywords]'))->getAttribute('content');
    $description = optional($document->first('meta[name=description]'))->getAttribute('content');

    // Добавить позже дополнительные проверки типа Keywords, Description

    if (strlen($h1) >= $dataLength) {
        $h1 = substr($h1, $offSet, $dataLength) . '...';
    }
    if (strlen($keywords) >= $dataLength) {
        $keywords = substr($keywords, $offSet, $dataLength) . '...';
    }
    if (strlen($description) >= $dataLength) {
        $description = substr($description, $offSet, $dataLength) . '...';
    }
    try {
        DB::beginTransaction();
        DB::table('url_checks')
            ->insert([
                'url_id' => $id,
                'status_code' => $response->status(),
                'h1' => mb_convert_encoding($h1, 'UTF-8'),
                'keywords' => mb_convert_encoding($keywords, 'UTF-8'),
                'description' => mb_convert_encoding($description, 'UTF-8'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        DB::table('urls')
            ->where('id', $id)
            ->update(['updated_at' => Carbon::now()]);
        DB::commit();
    } catch (Exception $e) {
        flash("Exception: {$e->getMessage()}")->error();
        return redirect(route('show.url', ['id' => $id]));
    }
    flash('Url was checked')->message();
    return redirect(route('show.url', ['id' => $id]));
})->name('check.url');
