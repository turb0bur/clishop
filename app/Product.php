<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku', 'name', 'price', 'quantity', 'booked', 'description'
    ];

    /**
     * Get the discounts for the product.
     */
    public function discounts()
    {
        return $this->hasMany('App\Discount');
    }

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
        if ($product->quantity > $quantity) {
            $product->quantity -= $quantity;
            $product->booked   += $quantity;

            return $product->save() ? $product : false;
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
        $product = Product::where('sku', strtolower($product_sku))->first();

        $product->booked   -= $quantity;
        $product->quantity += $quantity;

        return $product->save();
    }
}
