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
        $this->url = "http://test.com";
        $this->id = DB::table('urls')->insertGetId([
            'name' => $this->url,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
    public function testStore(): void
    {
        $fixturePath = 'tests/fixtures/testpage.html';
        $fakeHtml = file_get_contents($fixturePath);
        if ($fakeHtml === false) {
            throw new \Exception("Something wrong with fixtures file: {$fixturePath}");
        }
        $expectedData = [
            'url_id' => $this->id,
            'status_code' => 200,
            'keywords' => 'php, php.ru, форум php, php программ?...',
            'description' => 'Форум PHP программистов, док?...',
            'h1' => 'Новости'
        ];
        Http::fake([$this->url => Http::response($fakeHtml, 200)]);
        $response = $this->post(route('urls.checks.store', ['id' => $this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
}
