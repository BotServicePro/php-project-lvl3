<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    use RefreshDatabase; // трейт по сбросу бд для тестов каждый раз

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('main'));
        $response->assertOk();
    }

    public function testAllUrls()
    {
        $response = $this->get(route('allUrls'));
        $response->assertOk();
    }

//    public function testSingleUrl()
//    {
//        $urlData = ['name' => "http://test.com"];
//        $this->post(route('urls.store'), ['url' => $urlData]);
//        $response = $this->get(route('singleUrl', ['id' => 1]));
//        $response->assertOk();
//    }

    public function testSingleUrlNotFound()
    {
        DB::table('urls')->insert( // записываем в бд новый линк
            ['name' => 'http://123.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        $response = $this->get(route('singleUrl', ['id' => 88]));
        $response->assertStatus(404);
    }

    public function testStore(): void
    {
        $urlData = ['name' => 'https://example.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('urls', $urlData);
    }

    public function testIncorrectStoreUrl(): void
    {
        $urlData = ['name' => 'https:gmail.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasErrors();
    }

    public function testTooLongStoreUrl(): void
    {
        $tooLongDomain = str_repeat('domain', 20);
        $urlData = ['name' => "http://{$tooLongDomain}.com"];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHas('flash_notification.0.message', 'The url must not be greater than 100 characters.');
    }

    public function testInvalideStoreUrl(): void
    {
        $urlData = ['name' => "hp://test.com"];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHas('flash_notification.0.message', 'The url format is invalid.');
    }

    public function testAlreadyAddedStoreUrl(): void
    {
        DB::table('urls')->insert( // записываем в бд новый линк
            ['name' => 'http://test.com', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ); // заранее добавили запись в базу
        // если выполнить два метода post, то почему то проверка не пройдет
        $urlData = ['name' => "http://test.com"]; // сформировали новые данные
        $response = $this->post(route('urls.store'), ['url' => $urlData]); // запостили теже самые
        $response->assertSessionHas('flash_notification.0.message', 'The url has already been taken.');
    }

    public function testSuccessAddedStoreUrl(): void
    {
        $urlData = ['name' => "http://test.com"]; // сформировали новые данные
        $response = $this->post(route('urls.store'), ['url' => $urlData]); // запостили теже самые
        $response->assertSessionHas('flash_notification.0.message', 'Url was added!');
    }

    public function testUrlCheck(): void
    {
        $urlData = ['name' => 'https://google.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $addedUrlID = DB::table('urls')->where('name', $urlData['name'])->first()->id;
        $this->post(route('checkUrl', ['id' => $addedUrlID])); // сделали проверку домена
        DB::table('url_checks')->where('url_id', $addedUrlID)->first()->url_id;
        $checkData = ['url_id' => 1];
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('url_checks', $checkData);
    }

    public function testUrlSecondCheck(): void
    {
        $urlData = ['name' => 'https://google.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $addedUrlID = DB::table('urls')->where('name', $urlData['name'])->first()->id;
        $this->post(route('checkUrl', ['id' => $addedUrlID]));
        $this->post(route('checkUrl', ['id' => $addedUrlID]));
        $this->post(route('checkUrl', ['id' => $addedUrlID]));

        DB::table('url_checks')->where('url_id', $addedUrlID)->first()->id;
        $checkData = ['id' => 3];
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('url_checks', $checkData);
    }

    public function testCheckStatus200()
    {
        DB::table('urls')->insert(
            ['name' => 'https://php.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        $responseFromFixtures = file_get_contents('tests/fixtures/testpage.html');
        $expectedData = [
            'url_id' => 1,
            'status_code' => 200,
            'keywords' => 'php, php.ru, форум php, php программ?...',
            'description' => 'Форум PHP программистов, док?...',
            'h1' => 'Новости'
        ];
        Http::fake(['https://php.ru' => Http::response($responseFromFixtures, 200)]);
        $response = $this->post(route('checkUrl', ['id' => 1]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
}
