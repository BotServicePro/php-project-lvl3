<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    /**
     * Urls index test.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertStatus(200);
    }

    /**
     * Store url test.
     *
     * @return void
     */
    public function testStore(): void
    {
        $urlData = ['name' => 'https://example.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('show.url', ['id' => 1]));
        $this->assertDatabaseHas('urls', $urlData);
    }

    /**
     * Show single url test.
     *
     * @return void
     */
    public function testShow()
    {
        $id = DB::table('urls')->insertGetId(
            ['name' => "http://test.com", 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );
        $response = $this->get(route('show.url', ['id' => $id]));
        $response->assertOk();
        $response->assertStatus(200);
        $response->assertSee("http://test.com");
        $response = $this->get(route('show.url', ['id' => 777]));
        $response->assertStatus(404);
    }
}
