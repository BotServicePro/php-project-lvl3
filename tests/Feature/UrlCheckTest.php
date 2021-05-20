<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UrlCheckTest extends TestCase
{
    /** @var int */
    private $id;
    /** @var string */
    private $url;

    protected function setUp(): void
    {
        parent::setUp();
        $url = "http://test.com";
        $id = DB::table('urls')->insertGetId([
            'name' => $url,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $this->id = $id;
        $this->url = $url;
    }
    public function testStore(): void
    {
        $fakeHtml = file_get_contents('tests/fixtures/testpage.html');
        $expectedData = [
            'url_id' => 1,
            'status_code' => 200,
            'keywords' => 'php, php.ru, форум php, php программ?...',
            'description' => 'Форум PHP программистов, док?...',
            'h1' => 'Новости'
        ];
        Http::fake([$this->url => Http::response($fakeHtml, 200)]);
        $response = $this->post(route('check.url', ['id' => $this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
}
