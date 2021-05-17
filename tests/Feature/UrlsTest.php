<?php

namespace Tests\Feature;

use Tests\TestCase;

class UrlsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function TestIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $urlData = ['name' => "http://test.com"];
        $this->post(route('urls.store'), ['url' => $urlData]);
        $response = $this->get(route('show.url', ['id' => 1]));
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
