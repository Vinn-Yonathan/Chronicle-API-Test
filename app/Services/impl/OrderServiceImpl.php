<?php

namespace App\Services\impl;

use App\Jobs\FakeExternalAPICall;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderServiceImpl implements OrderService
{
    function add(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            DB::statement('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            $product = Product::find($orderData['product_id']);
            $orderData['total_price'] = $product->price * $orderData['quantity'];
            // Log::info($product->stock . '<-Stock - Quantity-> ' . $orderData['quantity']);

            if ($product->stock < $orderData['quantity']) {
                throw new HttpResponseException(response([
                    'errors' => [
                        'message' => ["Product's stock is unavailable"]
                    ]
                ], 409));
            } else {
                $product->update(['stock' => ($product->stock - $orderData['quantity'])]);
                $order = Order::create($orderData);
                // Log::info('Order:' . $order);

                // Simulasi call external api (Laravel queue)
                FakeExternalAPICall::dispatch($order);

            }

            return $order;
        });
    }
}