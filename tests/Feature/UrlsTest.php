<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    use RefreshDatabase; // трейт по сбросу бд для тестов каждый раз

    /**
     * A basic feature test example.
     *
     * @return void
     */
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        DB::table('urls')->insertGetId( // записываем в бд новый линк
//            ['name' => 'http://google.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
//        );
//        DB::table('urls')->insertGetId( // записываем в бд новый линк
//            ['name' => 'http://auto.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
//        );
//        DB::table('urls')->insertGetId( // записываем в бд новый линк
//            ['name' => 'http://test.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
//        );
//    }


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

    public function testSingleUrl()
    {
        $urlData = ['name' => "http://test.com"];
        $this->post(route('urls.store'), ['url' => $urlData]);
        $response = $this->get(route('singleUrl', ['id' => 1]));
        $response->assertOk();
    }

    public function testSingleUrlNotFound()
    {
        DB::table('urls')->insertGetId( // записываем в бд новый линк
            ['name' => 'http://123.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        $response = $this->get(route('singleUrl', ['id' => 88]));
        $response->assertStatus(500);
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
        DB::table('urls')->insertGetId( // записываем в бд новый линк
            ['name' => 'http://test.com', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ); // заранее добавили запись в базу

        $urlData = ['name' => "http://test.com"]; // сформировали новые данные
        $response = $this->post(route('urls.store'), ['url' => $urlData, $urlData]); // запостили теже самые
        $response->assertSessionHas('flash_notification.0.message', 'The url has already been taken.');
    }

}
