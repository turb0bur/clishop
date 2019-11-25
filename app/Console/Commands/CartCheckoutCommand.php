<?php

namespace App\Console\Commands;

use App\Cart;
use App\Order;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class CartCheckoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:checkout
                            {--C|cid= : ID of the cart}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all added to the cart products, the cart\'s total and provide to the checkout process';

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
        $cart_id = $this->option('cid');

        $validator = Validator::make([
            'cart_id' => $cart_id,
        ], [
            'cart_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $this->line('Something went wrong. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return;
        }

        $cart  = Cart::findOrFail($cart_id);
        $total = $cart->total;

        $headers = ['SKU', 'Name', 'Quantity', 'Subtotal', 'Present', 'Notes'];
        $this->table($headers, $cart->order);
        $this->info("Your total is \${$total}");
        $choice = $this->choice('Would you like to proceed with the payment process(p), cancel your order(c) or come back to the shop(b)? (p/c/b)?', ['p', 'c', 'b']);

        switch ($choice):
            case 'p':
                $card_number = $this->ask('Type your credit card number, please (12 digits)');
//                $exp_date    = $this->ask('Type your credit card expiration date, please (mm/yy)');
//                $cvv         = $this->secret('Type your credit card CVV2 code (3 digits)');
                $username = $this->ask('Type your credit card cardholder, please');
                $email    = $this->ask('Type your email, please');
                $phone    = $this->ask('Type your phone number, please');
//                $address     = $this->ask('Type your delivery address, please');

                $validator = Validator::make([
                    'card_number' => $card_number,
//                    'exp_date'    => $exp_date,
//                    'cvv'         => $cvv,
                    'username'    => $username,
                    'email'       => $email,
                    'phone'       => $phone,
//                    'address'     => $address,
                ], [
                    'card_number' => 'required|numeric|digits:12',
//                    'exp_date'    => 'required|date_format:m/y|after:today',
//                    'cvv'         => 'required|numeric|digits:3',
                    'username'    => 'required|string',
                    'email'       => 'required|email',
                    'phone'       => 'required|string|max:13',
//                    'address'     => 'required|string',
                ]);

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $error) {
                        $this->error($error);
                    }

                    return;
                }

                $user = User::where(['email' => $email])->first() ?:
                    User::create([
                        'name'     => $username,
                        'email'    => $email,
                        'phone'    => $phone,
                        'address'  => '36, Shchyretska St., Lviv',
                        'password' => bcrypt('password'),
                    ]);

                $order = Order::create([
                    'user_id'     => $user->id,
                    'total'       => $total,
                    'credit_card' => $card_number,
                    'payment'     => Order::CC_PAYMENT,
                    'delivery'    => Order::COURIER_DELIVERY,
                    'order'       => json_encode($cart->order),
                ]);
                if ($order) {
                    $cart->forceDelete();
                    $this->info('You have successfully placed your order. Please check your email to see more details');
                    $this->call('products:all');
                }

                return true;
            case 'c':
                if ($this->confirm('Are you sure you want to delete your order?')) {
                    $cart->delete();
                }
                break;
            case 'b':
            default:
                return;
        endswitch;
    }
}
