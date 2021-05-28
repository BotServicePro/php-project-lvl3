<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
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
Route::get('/', function (Request $request): object {
    $url = $request->old('url', ['name' => null]);
    return view('index', compact('url'));
})->name('main.page');

Route::get('/urls', function (): object {
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
    return view('urls.index', compact('urls', 'lastChecks'));
})->name('urls.index');

Route::post('/urls', function (Request $request): Illuminate\Http\RedirectResponse {
    $url = $request->input('url');
    $parsedUrl = parse_url($url['name']);
    $validator = Validator::make($url, [
        'name' => 'required|url|max:100',
    ]);
    $errorMessage = $validator
        ->errors()
        ->first('name');
    if ($validator->fails()) {
        flash($errorMessage)->error();
        return redirect(route('main.page'))
            ->withErrors($validator)
            ->withInput();
    }
    $validatedUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";
    $existigUrl = DB::table('urls')->where('name', $validatedUrl)->exists();
    if ($existigUrl) {
        flash(__('validation.unique'))->warning();
        $id = DB::table('urls')
            ->where('name', $validatedUrl)
            ->value('id');
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
    return redirect(route('urls.show', ['id' => $id]));
})->name('urls.store');

Route::get('/urls/{id}', function ($id): Illuminate\View\View {
    $url = DB::table('urls')->find($id);
    abort_unless($url, 404);
    $checksData = DB::table('url_checks')
        ->where('url_id', '=', $id)
        ->latest()
        ->get();
    return view('urls.show', compact('url', 'checksData'));
})->name('urls.show');

Route::post('urls/{id}/checks', function ($id): Illuminate\Http\RedirectResponse {
    $url = DB::table('urls')->find($id)->name;
    abort_unless($url, 404);
    try {
        $response = Http::get($url);
        $document = new Document($response->body());
        $h1 = optional($document->first('h1'))->text();
        if(strlen($h1) > 200) {
            $h1 = substr($h1, '0', '70');
        }


        $keywords = optional($document->first('meta[name=keywords]'))->getAttribute('content');
        if ($keywords === null) {
            $keywords = optional($document->first('meta[name=Keywords]'))->getAttribute('content');
        }
        $description = optional($document->first('meta[name=description]'))->getAttribute('content');
        if ($description === null) {
            $description = optional($document->first('meta[name=Description]'))->getAttribute('content');
        }


//        dump($h1);
//        dump($keywords);
//        dump($description);
//        exit;
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
        flash('Url was checked')->message();
    } catch (RequestException | ConnectionException $e) {
        flash("Exception: {$e->getMessage()}")->error();
    }
    return redirect(route('urls.show', ['id' => $id]));
})->name('urls.checks.store');
