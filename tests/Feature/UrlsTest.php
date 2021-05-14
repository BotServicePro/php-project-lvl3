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
    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
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
        $this->assertDatabaseHas('urls', $urlData);
    }

    public function testIncorrectStoreUrl(): void
    {
        $urlData = ['name' => 'https:gmail.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasErrors();
    }

    public function testTooLongStoreUrl(): void
    {
        $tooLongDomain = str_repeat('domain', 20);
        $urlData = ['name' => "http://{$tooLongDomain}.com"];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHas('flash_notification.0.message', 'The url must not be greater than 100 characters.');
    }

    public function testInvalideStoreUrl(): void
    {
        $urlData = ['name' => "hp://test.com"];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHas('flash_notification.0.message', 'The url format is invalid.');
    }
}
