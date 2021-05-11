<?php

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\QueryException;
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
    $urlsData = DB::table('urls')
        ->orderBy('id', 'asc')
        ->paginate($linksPerPage);
    $checksData = DB::table('url_checks')
        ->distinct('url_id')
        ->orderBy('url_id')
        ->orderBy('created_at', 'desc')
        ->get()
        ->keyBy('url_id');
    return view('urls/index', compact('urlsData', 'checksData'));
})->name('urls.index');

Route::post('/urls', function (Request $request) {
    $url = mb_strtolower($request->input('url')['name']);
    $parsedUrl = parse_url($url);
    $rules = [
        'url.name' => 'bail|required|url|max:100|unique:urls,name'
    ];
    $validator = Validator::make($request->all('url'), $rules);
    $errorMessage = $validator
        ->errors()
        ->first('url.name');
    if ($validator->fails()) {
        if ($errorMessage === __('validation.unique')) { // see resources/lang/en/validation.php
            $id = DB::table('urls')
                ->where('name', "{$parsedUrl['scheme']}://{$parsedUrl['host']}")
                ->first()->id;
            return redirect(route('show.url', ['id' => $id]))
                ->withErrors(flash(__('validation.unique'))
                    ->warning());
        }
        return redirect(route('main.page'))
            ->withErrors(flash($errorMessage)
                ->error());
    }
    $addedUrlID = DB::table('urls')->insertGetId(
        [
            'name' => "{$parsedUrl['scheme']}://{$parsedUrl['host']}",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]
    );
    flash('Url was added!')->success();
    return redirect(route('show.url', ['id' => $addedUrlID]));
})->name('urls.store');

Route::get('/url/{id}', function ($id) {
    $urlData = DB::table('urls')
        ->where('id', $id)
        ->first();

    if ($urlData === null) {
        flash("Url does not exists!")->error();
        return redirect(route('urls.index'))->setStatusCode(404);
    }

    $checksData = DB::table('url_checks')
        ->where('url_id', '=', $id)
        ->orderBy('updated_at', 'desc')
        ->get();
    return view('urls/show', compact('urlData', 'checksData'));
})->name('show.url');

Route::post('url/{id}/checks', function ($id) {
    $url = DB::table('urls')
        ->where('id', '=', $id)
        ->first()->name;

    try {
        $document = new Document($url, true);
    } catch (RuntimeException $e) {
        flash("ConnectException: {$e->getMessage()}")->error();
        return redirect(route('show.url', ['id' => $id]));
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
        $statusCode = $client
            ->request('GET', $url)
            ->getStatusCode();
    } catch (ConnectException $e) {
        flash("ConnectException: {$e->getMessage()}")->error();
        return redirect(route('show.url', ['id' => $id]));
    } catch (ClientException $e) {
        $statusCode = $e->getResponse()->getStatusCode();
    } catch (RequestException $e) {
        flash("RequestException: {$e->getMessage()}")->error();
        return redirect(route('show.url', ['id' => $id]));
    }

    try {
        DB::beginTransaction();
        DB::table('url_checks')
            ->insert([
                'url_id' => $id,
                'status_code' => $statusCode,
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
    } catch (QueryException $e) {
        flash("RequestException: {$e->getMessage()}")->error();
        return redirect(route('show.url', ['id' => $id]));
    }
    flash('Url was checked')->message();
    return redirect(route('show.url', ['id' => $id]));
})->name('check.url');
