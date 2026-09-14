<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_books(): void
    {
        $response = $this->get('/api/books');

        $response->assertStatus(200);
    }

    public function test_can_list_authors(): void
    {
        $response = $this->get('/api/authors');

        $response->assertStatus(200);
    }

    public function test_can_create_author(): void
    {
        $response = $this->postJson('/api/authors', [
            'name' => 'Test Author',
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_create_author_without_name(): void
    {
        $response = $this->postJson('/api/authors', []);

        $response->assertStatus(422);
    }
}
