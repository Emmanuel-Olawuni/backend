<?php

namespace Tests\Feature;

use App\Models\User;
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
    public function text_login(){
        User::factory()->create([
            'name' => 'emmanuel Olawuni',
            'password' => bcrypt('password')
        ]);
        $response = $this->postJson('/api/login', [
            'email'=> 'emma@gmail.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }
}
