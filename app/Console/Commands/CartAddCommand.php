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
                            {--cid= : Use retrieved cart ID in order to continue your shopping}
                            {--Q|quantity=1 : The amount of the chosen product}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adding product to a new or an existed cart';

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
        }
        $order = [
            'sku'      => $product_sku,
            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity,
        ];
        if ($cart_id) {
            $cart       = Cart::findOrFail($cart_id);
            $cart_order = json_decode($cart->order, true);
            foreach ($cart_order as $key => $item) {
                if (in_array($product_sku, $item)) {
                    $cart_order[$key]['quantity'] += $quantity;
                    $cart_order[$key]['subtotal'] = $product->price * $cart_order[$key]['quantity'];
                } else {
                    $cart_order[] = $order;
                }
            }
            $cart->update(['order' => json_encode($cart_order)]);
            $this->info("Your cart (cid={$cart->id}) has been successfully updated.");
            $headers = ['SKU', 'Quantity', 'Subtotal'];
            $this->table($headers, $cart_order);
        } else {
            $cart = Cart::create(['order' => json_encode([$order])]);
            $this->info("The product has been successfully added. Your cart ID is {$cart->id}. Use it in case you want to proceed with the purchase order");
        }
    }
}
