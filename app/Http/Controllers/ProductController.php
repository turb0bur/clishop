<?php

namespace App\Http\Controllers;

use App\Product;

class ProductController extends Controller
{
    /**
     * Check whether enough amount for the product in the stock
     *
     * @param string  $product_sku
     * @param integer $quantity
     * @return Product|boolean
     */
    public static function reserveIfAvailable($product_sku, $quantity = 1)
    {
        $product = Product::where('sku', strtolower($product_sku))->first();
        if ($product->quantity >= $quantity) {
            $product->quantity -= $quantity;
            $product->booked   += $quantity;

            return $product->save() ? $product->booked : false;
        } elseif ($product->quantity < $quantity && $product->quantity > 0) {
            $product->booked   += $product->quantity;
            $product_reserved  = $product->quantity;
            $product->quantity = 0;

            return $product->save() ? $product_reserved : false;
        } else {
            return false;
        }
    }

    /**
     * Make products available for purchase after removing from a cart
     *
     * @param string  $product_sku
     * @param integer $quantity
     * @return boolean
     */
    public static function removeReservation($product_sku, $quantity)
    {
        $product = Product::where('sku', $product_sku)->first();

        $product->booked   -= $quantity;
        $product->quantity += $quantity;

        return $product->save();
    }
}
