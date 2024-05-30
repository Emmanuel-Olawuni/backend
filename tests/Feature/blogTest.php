<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class blogTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example()
    {
        $response = $this->postJson('/api/register' , [
            'name'=> ' Emmanuel',
            'email'=> 'emma@gmail.com',
            'password' => 'Emma2001*'
        ]);
        $response->assertStatus(201);

    }
}
