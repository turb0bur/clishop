<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'total', 'credit_card', 'payment', 'delivery', 'order'
    ];

    /**
     * Get the user that made the order.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
