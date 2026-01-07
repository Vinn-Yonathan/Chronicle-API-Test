<?php

namespace App\Services\Impl;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;

class ProductServiceImpl implements ProductService
{

    function add(array $productData): Product
    {
        return Product::create($productData);
    }

    function get(int $id): ?Product
    {
        return Product::find($id);
    }

    function getList(): Collection
    {
        return Product::all();
    }

    function update(int $id, array $productData): ?Product
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($productData);
        }
        return $product;
    }

    function delete(int $id): ?bool
    {
        $product = Product::find($id);
        if ($product) {
            return $product->delete();
        }

        return null;
    }
}