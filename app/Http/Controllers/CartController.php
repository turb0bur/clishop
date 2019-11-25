<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Discount;
use App\Product;

class CartController extends Controller
{
    /**
     * The cart being operated.
     *
     * @var Cart
     */
    protected $cart;

    /**
     * The ordered products of the current curt.
     *
     * @var array
     */
    protected $order;

    /**
     * Create a new controller instance.
     *
     * @param integer $cart_id
     * @return void
     */
    public function __construct($cart_id)
    {
        $this->cart  = Cart::findOrFail($cart_id);
        $this->order = $this->cart->order;
    }

    /**
     * Checking each cart product for available discount and apply if exists
     *
     * @return  void
     */
    public function applyDiscounts()
    {
        if (!empty($cart_order = &$this->order)) {
            $this->removePresents();
            foreach ($cart_order as $key => $item) {
                $product  = Product::where(['sku' => $item['sku']])->firstOrFail();
                $discount = $product->discount;
                if ($item['present'] === 'Yes' || !$discount) {
                    continue;
                }

                switch ($discount->type):
                    case Discount::PERCENT_PRODUCT_DISCOUNT:
                        if ($item['quantity'] >= 3) {
                            $cart_order[$key]['subtotal'] -= round($item['subtotal'] * $discount->value / 100, 2);
                            $cart_order[$key]['notes']    = "You got {$discount->value}% discount for this product";

                            $this->cart->update(['order' => $cart_order]);
                        }
                        break;
                    case Discount::PRODUCT_FOR_FREE:
                        $present_quantity = intdiv($item['quantity'], $discount->value);
                        $present          = Product::findOrFail($discount->present_id);
                        $present_order    = [
                            'sku'      => $present->sku,
                            'name'     => $present->name,
                            'quantity' => $present_quantity,
                            'subtotal' => 0,
                            'present'  => 'Yes',
                            'notes'    => "Having bought {$item['quantity']} {$item['name']} you got $present_quantity {$present->name} for free"
                        ];
                        if (Product::reserveIfAvailable($present->sku, $present_quantity)) {
                            $cart_order[] = $present_order;
                            $this->cart->update(['order' => $cart_order]);
                        }
                        break;
                endswitch;
            }
        }
    }

    /**
     * Remove the products from the cart that had been added as presents
     *
     * @return  boolean
     */
    public function removePresents()
    {
        if (!empty($cart_order = &$this->order)) {
            foreach ($cart_order as $key => $item) {
                if ($item['present'] === 'Yes') {
                    if (Product::removeReservation($item['sku'], $item['quantity'])) {
                        unset($cart_order[$key]);
                    }
                }
            }

            return $this->cart->update(['order' => $cart_order]);
        }
    }
}
