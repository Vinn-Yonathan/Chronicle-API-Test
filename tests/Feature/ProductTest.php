<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ProductTest extends TestCase
{
    // Use this (uncomment) for inmemory sqlite db test 
    use RefreshDatabase;

    // // Use this (uncomment) for mysql db test 
    // protected function setUp(): void
    // {
    //     parent::setUp();
    //     DB::delete('delete from products');
    // }

    /**
     * A basic feature test example.
     */
    public function testAddProductSuccess(): void
    {
        $response = $this->post('/api/products', [
            'name' => 'Buku',
            'stock' => 10,
            'price' => 5000,
        ]);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'id' => 1,
                'name' => 'Buku',
                'stock' => 10,
                'price' => 5000,
            ]
        ]);
    }
    public function testAddProductFailedValidation(): void
    {
        $response = $this->post('/api/products', [
            'stock' => -1,
            'price' => "1s",
        ]);

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'name' => ['The name field is required.'],
                'stock' => ['The stock field must be at least 0.'],
                'price' => ['The price field must be a number.'],
            ]
        ]);
    }

    public function testGetProductSuccess(): void
    {
        $this->seed([ProductSeeder::class]);
        $product = Product::find(1)->first();

        $response = $this->get('/api/products/' . $product->id);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => 1,
                'name' => 'Buku 1',
                'stock' => 10,
                'price' => 5000,
            ]
        ]);
    }
    public function testGetProductNotFound(): void
    {
        $this->seed([ProductSeeder::class]);

        $response = $this->get('/api/products/100');

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'message' => ['Data not found'],
            ]
        ]);
    }

    public function testGetProductListSuccess(): void
    {
        $this->seed([ProductSeeder::class]);

        $response = $this->get('/api/products')->assertStatus(200)->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        $this->assertCount(10, $response['data']);
    }
    public function testGetProductListNotFound(): void
    {

        $response = $this->get('/api/products');

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'message' => ['Data not found'],
            ]
        ]);
    }

    public function testUpdateProductSuccess(): void
    {
        $this->seed([ProductSeeder::class]);
        $product = Product::find(1);
        $response = $this->put('/api/products/' . $product->id, [
            'name' => 'Buku tulis',
            'stock' => 11
        ]);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => 1,
                'name' => 'Buku tulis',
                'stock' => 11,
                'price' => 5000,
            ]
        ]);
    }
    public function testUpdateProductFailedValidation(): void
    {
        $this->seed([ProductSeeder::class]);
        $product = Product::find(1);
        $response = $this->put('/api/products/' . $product->id, [
            'name' => '',
            'stock' => 11
        ]);

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'name' => ['The name field is required.'],
            ]
        ]);
    }
    public function testUpdateProductNotFound(): void
    {
        $response = $this->put('/api/products/1', [
            'name' => 'Buku tulis',
            'stock' => 11
        ]);

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'message' => ['Data not found'],
            ]
        ]);
    }
    public function testDeleteProductSuccess(): void
    {
        $this->seed(ProductSeeder::class);
        $product = Product::find(1)->first();
        $response = $this->delete('/api/products/' . $product->id);
        Log::info($product);

        $response->assertStatus(200)->assertJson([
            'data' => true
        ]);

        $deletedProduct = Product::withTrashed()->find(1);
        Log::info($deletedProduct);
        $this->assertNotNull($deletedProduct->deleted_at);
    }
    public function testDeleteProductNotFound(): void
    {
        $response = $this->delete('/api/products/1');

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'message' => ['Data not found'],
            ]
        ]);
    }


}
