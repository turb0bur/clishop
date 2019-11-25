<?php

namespace App\Console\Commands;

use App\Cart;
use App\Http\Controllers\CartController;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CartAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:add 
                            {product : The SKU of the product}
                            {--C|cid= : Use retrieved cart ID in order to continue your shopping}
                            {--Q|quantity=1 : The amount of the chosen product}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add product to a new or an existed cart';

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
        $quantity    = $this->option('quantity') ?: 1;

        $validator = Validator::make([
            'product'  => $product_sku,
            'cart_id'  => $cart_id,
            'quantity' => $quantity,
        ], [
            'product'  => 'string|size:6',
            'cart_id'  => 'nullable|numeric',
            'quantity' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $this->line('The product has not been added. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return;
        }

        if (!$product = Product::reserveIfAvailable($product_sku, $quantity)) {
            $this->error('There are not enough product in the stock.');

            return;
        }
        $order = [
            'sku'      => $product_sku,
            'name'     => $product->name,
            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity,
            'present'  => 'No',
            'notes'    => '',
        ];
        if ($cart_id) {
            $cart            = Cart::findOrFail($cart_id);
            $cart_order      = $cart->order;
            if (!empty($cart_order)) {
                $in_cart = false;
                foreach ($cart_order as $key => $item) {
                    if ($item['sku'] == $product_sku && $item['present'] !== 'Yes') {
                        $cart_order[$key]['quantity'] += $quantity;
                        $cart_order[$key]['subtotal'] = round($product->price * $quantity, 2);

                        $in_cart = true;
                    }
                }
                if (!$in_cart) {
                    $cart_order[] = $order;
                }
                $cart->update(['order' => $cart_order]);
                $cart_operations = new CartController($cart_id);
                $cart_operations->applyDiscounts();
                $this->info("You have successfully added $quantity {$product->name} to your cart (cid={$cart->id}).");
            }
        } else {
            $cart = Cart::create(['order' => [$order]]);

            $cart_operations = new CartController($cart->id);
            $cart_operations->applyDiscounts();
            $this->info("The product has been successfully added. Your cart ID is {$cart->id}. Use it in case you want to proceed with the purchase order");
        }
    }
}
