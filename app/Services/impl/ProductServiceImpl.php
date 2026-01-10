<?php

namespace App\Services\Impl;

use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductServiceImpl implements ProductService
{

    function add(array $productData): Product
    {
        return Product::create($productData);
    }

    function get(int $id): ?Product
    {
        try {
            if (Cache::has('product#' . $id)) {
                // Log::info(json_decode(Cache::get('product#' . $id), true));
                return new Product(json_decode(Cache::get('product#' . $id), true));
            }
        } catch (Exception $e) {
            Log::info($e);
        }

        $product = Product::find($id);
        if ($product) {
            try {
                Cache::put('product#' . $id, json_encode($product), 300);
            } catch (Exception $e) {
                Log::info($e);
            }
        }

        return $product;
    }

    function getList(): Collection
    {
        return Product::all();
    }

    function update(int $id, array $productData): ?Product
    {
        $product = $this->get($id);
        if ($product) {
            $product->update($productData);
            try {
                Cache::put('product#' . $id, json_encode($product), 300);
            } catch (Exception $e) {
                Log::info($e);
            }
        }
        return $product;
    }

    function delete(int $id): ?bool
    {
        $product = $this->get($id);
        if ($product) {
            $isDeleted = $product->delete();
            try {
                Cache::forget('product#' . $id);
            } catch (Exception $e) {
                Log::info($e);
            }
            return $isDeleted;
        }

        return null;
    }
}


