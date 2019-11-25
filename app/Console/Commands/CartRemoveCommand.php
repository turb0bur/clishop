<?php

namespace App\Console\Commands;

use App\Cart;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CartRemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:remove
                            {product : The SKU of the product}
                            {--C|cid= : Use retrieved cart ID in order to continue your shopping}
                            {--Q|quantity= : The amount of the product that will be removed from the cart. The whole number will be removed if quantity missed }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decrease quantity or remove the product from the cart';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $product_sku = $this->argument('product');
        $cart_id     = $this->option('cid');
        $quantity    = $this->option('quantity') ?: 0;

        $validator = Validator::make([
            'product'  => $product_sku,
            'cart_id'  => $cart_id,
            'quantity' => $quantity,
        ], [
            'product'  => 'string|size:6',
            'cart_id'  => 'required|numeric',
            'quantity' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $this->line('The product has not been removed. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return;
        }

        $cart = Cart::findOrFail($cart_id);
        if (!$cart) {
            $this->error('Cart with the provided ID has not been found');

            return;
        }

        $product    = Product::where(['sku' => $product_sku])->first();
        $cart_order = $cart->order;
        foreach ($cart_order as $key => $item) {
            if ($item['sku'] == $product_sku) {
                if ($quantity > 0 && $quantity < $item['quantity']) {
                    $cart_order[$key]['quantity'] -= $quantity;
                    $cart_order[$key]['subtotal'] = $product->price * $cart_order[$key]['quantity'];
                } elseif ($quantity == 0) {
                    unset($cart_order[$key]);
                    break;
                }
            }
        }
        $cart->update(['order' => $cart_order]);
        Product::removeReservation($product_sku, $quantity);
        $this->info("Your cart (cid={$cart->id}) has been successfully updated.");
        $headers = ['SKU', 'Quantity', 'Subtotal'];
        $this->table($headers, $cart_order);
    }
}
