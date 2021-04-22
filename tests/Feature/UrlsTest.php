<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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

    public function addTestDataToBase()
    {
        DB::table('urls')->insertGetId( // записываем в бд новый линк
            ['name' => 'http://google.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        DB::table('urls')->insertGetId( // записываем в бд новый линк
            ['name' => 'http://auto.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        DB::table('urls')->insertGetId( // записываем в бд новый линк
            ['name' => 'http://test.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
    }

    public function testRequestMainPage()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testRequestUrlsPage()
    {
        $response = $this->get('/urls');
        $response->assertStatus(200);
    }

    public function testRequestSingleUrlPage()
    {
        self::addTestDataToBase();
        $response = $this->get('/url/3');
        $response->assertStatus(200);
    }

    public function testUrl()
    {
        DB::table('urls')->insertGetId( // записываем в бд новый линк
            ['name' => 'http://123.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        $response = $this->get('/url/1');
        $response->assertStatus(200);
    }

//    public function testUrlBody()
//    {
//        DB::table('urls')->insertGetId( // записываем в бд новый линк
//            ['name' => 'http://123.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
//        );
//        $response = $this->get('/url/1');
//        $response->assertSee("<td>http://123.ru</td>");
//    }
}
