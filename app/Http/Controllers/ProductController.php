<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductAddRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

use function Symfony\Component\Translation\t;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    private function handleNotFound(Product|array|bool|null $product)
    {
        if ($product)
            return;

        throw new HttpResponseException(response(
            [
                'errors' => [
                    'message' => ["Data not found"]
                ]
            ],
            404
        ));
    }

    public function add(ProductAddRequest $request): JsonResponse
    {
        $productData = $request->validated();
        $product = $this->productService->add($productData);
        $productResource = new ProductResource($product);
        return $productResource->response()->setStatusCode(201);
    }

    public function get(int $id): ProductResource
    {
        $product = $this->productService->get($id);

        $this->handleNotFound($product);
        return new ProductResource($product);
    }

    public function getList(): ProductCollection
    {
        $products = $this->productService->getList();

        Log::info($products);

        $this->handleNotFound($products->all());
        return new ProductCollection($products);
    }

    public function update(ProductUpdateRequest $request, int $id): ProductResource
    {
        $productData = $request->validated();
        $product = $this->productService->update($id, $productData);
        $this->handleNotFound($product);
        return new ProductResource($product);
    }

    public function delete(int $id): Response
    {
        $isDeleted = $this->productService->delete($id);
        $this->handleNotFound($isDeleted);

        return response(['data' => true])->setStatusCode(200);
    }


}
