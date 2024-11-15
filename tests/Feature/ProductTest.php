<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_a_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create(['name' => 'Sample Category']);

        $response = $this->postJson('/api/products', [
            'name' => 'New Product',
            'description' => 'Description.',
            'price' => 99.99,
            'stock' => 10,
            'categories' => [$category->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'name', 'description', 'price', 'stock']]);

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'price' => 99.99,
        ]);

        $productId = Product::where('name', 'New Product')->first()->id;

        $this->assertDatabaseHas('category_product', [
            'product_id' => $productId,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create(['name' => 'Sample Category']);
        $product = Product::create([
            'name' => 'Old Product',
            'description' => 'Old Description.',
            'price' => 50.00,
            'stock' => 5,
        ]);

        $product->categories()->attach($category->id);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated Description.',
            'price' => 75.00,
            'stock' => 10,
            'categories' => [$category->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'description', 'price', 'stock']]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 75.00,
        ]);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function it_fails_to_update_a_product_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::create([
            'name' => 'Old Product Name',
            'description' => 'Old Description',
            'price' => 10.00,
            'stock' => 15,
        ]);

        $invalidUpdateData = [
            'name' => '', // Invalid: name cannot be empty
            'description' => 'Updated Description',
            'price' => 20.00,
            'stock' => 5,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $invalidUpdateData);

        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotEmpty($response->json()['errors']['name']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Old Product Name',
            'description' => 'Old Description',
            'price' => 10.00,
            'stock' => 15,
        ]);
    }

    /** @test */
    public function it_deletes_a_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10.00,
            'stock' => 10,
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertNotNull($product->fresh()->deleted_at);
    }
}
