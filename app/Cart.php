<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order' => 'array'
    ];

    /**
     * Decode the cart order.
     *
     * @param  string $value
     * @return array
     */
    public function getOrderAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Get calculated cart total
     *
     * @return  float
     */
    public function getTotalAttribute()
    {
        $order = $this->order;
        $total = 0;

        foreach ($order as $key => $item) {
            $total += $order[$key]['subtotal'];
        }

        return $total;
    }
}
