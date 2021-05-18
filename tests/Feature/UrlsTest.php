<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('urls')->insert([
            [
                'id' => 1,
                'name' => 'http://test.ru'
            ],
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testStore()
    {
        $response = $this->post(route('urls.store'), ['url' => ['name' => 'http://example']]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('urls.show', ['id' => 2]));
    }

    public function testShowNotFound()
    {
        $response = $this->get(route('urls.show', ['id' => 2]));
        $response->assertNotFound();
    }

    public function testShow()
    {
        $response = $this->get(route('urls.show', ['id' => 1]));
        $response->assertOk();
    }
}
