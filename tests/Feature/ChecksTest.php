<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChecksTest extends TestCase
{
    /** @var int */
    private $id = 1;
    /** @var string */
    private $name = "http://test.com";

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('urls')->insert([
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
    public function testUrlCheck()
    {
        $fakeHtml = file_get_contents('tests/fixtures/testpage.html');
        $expectedData = [
            'url_id' => $this->id,
            'status_code' => 200,
            'keywords' => 'php, php.ru, форум php, php программ?...',
            'description' => 'Форум PHP программистов, док?...',
            'h1' => 'Новости'
        ];
        Http::fake([$this->name => Http::response($fakeHtml, 200)]);
        $response = $this->post(route('check.url', ['id' => $this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
}
