<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChecksTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUrlCheck()
    {
        DB::table('urls')->insert(
            ['name' => 'https://php.ru', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        $fakeHtml = file_get_contents('tests/fixtures/testpage.html');
        $expectedData = [
            'url_id' => 1,
            'status_code' => 200,
            'keywords' => 'php, php.ru, форум php, php программ?...',
            'description' => 'Форум PHP программистов, док?...',
            'h1' => 'Новости'
        ];
        Http::fake(['https://php.ru' => Http::response($fakeHtml, 200)]);
        $response = $this->post(route('check.url', ['id' => 1]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
}
