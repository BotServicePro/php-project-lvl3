<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    private $id = 1;

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('urls')->insert([
            'id' => $this->id,
            'name' => "http://test.com",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $response = $this->get(route('urls.show', ['id' => $this->id]));
        $response->assertOk();
    }

    public function testStore(): void
    {
        $urlData = ['name' => 'https://example.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('urls.show', ['id' => 2]));
        $this->assertDatabaseHas('urls', $urlData);
    }
}
