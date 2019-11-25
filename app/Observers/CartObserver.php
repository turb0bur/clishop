<?php

namespace App\Observers;

use App\Cart;
use App\Product;

class CartObserver
{
    /**
     * Handle the Cart "deleting" event.
     *
     * @param Cart $cart
     * @return void
     */
    public function deleting(Cart $cart)
    {
        $cart_order = $cart->order;
        if (!empty($cart_order)) {
            foreach ($cart_order as $key => $item) {
                $product = Product::where('sku', $item['sku'])->first();
                if ($cart->isForceDeleting()) {
                    $product->booked -= $item['quantity'];
                    $product->update(['booked']);
                } else {
                    $product->booked -= $item['quantity'];
                    $product->quantity += $item['quantity'];
                    $product->update(['booked', 'quantity']);
                }
            }
        }
    }
}
