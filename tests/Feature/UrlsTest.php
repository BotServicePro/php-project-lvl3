<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    /** @var int */
    private $id;

    protected function setUp(): void
    {
        parent::setUp();
        $this->id = DB::table('urls')->insertGetId([
            'name' => "http://test.com",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function testIndex(): void
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testShow(): void
    {
        $response = $this->get(route('urls.show', ['id' => $this->id]));
        $response->assertOk();
        $response->assertStatus(200);
    }

    public function testStore(): void
    {
        $urlData = ['name' => 'https://example.com'];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('urls.show', ['id' => 2]));
        $this->assertDatabaseHas('urls', $urlData);
    }

    public function testEmptyUrlStore(): void
    {
        $urlData = ['name' => ''];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasErrors('name', __('validation.required'));
    }

    public function testTooLongUrlStore(): void
    {
        $tooLongDomain = str_repeat('domain', 20);
        $urlData = [
            'name' => "http://{$tooLongDomain}.com"
        ];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertSessionHasErrors('name', __('validation.string'));
    }

    public function testExistsUrlStore(): void
    {
        $urlData = ['name' => "http://test.com"];
        $checkData = ['id', 2];
        $response = $this->post(route('urls.store'), ['url' => $urlData]);
        $response->assertRedirect(route('urls.show', ['id' => 1]));
        $this->assertDatabaseMissing('urls', $checkData);
    }
}
