<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $url = "http://test.com";
        $id = DB::table('urls')->insertGetId(
            ['name' => $url, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        $response = $this->get(route('show.url', ['id' => $id]));
        //$response->assertStatus(200);
        $response->assertOk();
    }

    public function testStore(): void
    {
        $urlData = ['name' => 'https://example.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('show.url', ['id' => 1]));
        $this->assertDatabaseHas('urls', $urlData);
    }
}
