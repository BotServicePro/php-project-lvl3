<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UrlsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function TestRequestMainPage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function TestRequestUrlsPage()
    {
        $response = $this->get('/urls');

        $response->assertStatus(200);
    }

}
