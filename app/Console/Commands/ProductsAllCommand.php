<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class ProductsAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all products table';

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
        $headers = ['SKU', 'Name', 'Price', 'Quantity'];
        $products = Product::all(['sku', 'name', 'price', 'quantity'])->toArray();
        $this->table($headers, $products);
    }
}
