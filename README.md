# Laravel CLI Shop

## Installation

The application could be installed as an ordinary Laravel 6 application.
In order to avoid any problems with configuring an environment I would recommend use [Laravel Homestead](https://laravel.com/docs/6.x/homestead)

## Usage

Run migrations with seed option to create database structure and seed it with a test data 
``` bash
$ php artisan migrate --seed
```

##### Show all shop's products
``` bash
$ php artisan products:all
```

##### Add some amount of product to the cart
``` bash
$ php artisan cart:add {product_sku} --cid={cart_id} --quantity={quantity}
```
If the option cid is not passed new cart will be created. Use it to proceed your purchases.
If the option quantity is not passed the only one will be added. 

##### Remove some amount of the product from the cart
``` bash
$ php artisan cart:remove {product_sku} --cid={cart_id} --quantity={quantity}
```
The option cid is required 

##### Show your cart and(or) proceed with checkout
``` bash
$ php artisan cart:checkout --cid={cart_id}
```


For all options could be used shortcuts. Example:
``` bash
$ php artisan cart:add 5fd3ty -C1 -Q3
```
This command adds 3 products with SKU 5fd3ty to the cart with ID 1
