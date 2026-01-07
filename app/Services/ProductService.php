<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductService
{
    function add(array $productData): Product;
    function getList(): Collection;
    function get(int $id): ?Product;
    function update(int $id, array $productData): ?Product;
    function delete(int $id): ?bool;
}