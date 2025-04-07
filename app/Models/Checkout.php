<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $table = 'checkout'; // Table name

    protected $fillable = [
        'cart_id',
        'user_id',
        'provider_id',
        'category_id',
        'product_id',
        'name',
        'mobile',
        'email',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'pincode',
        'payment_method',
        'grand_total',
        'status',
    ];

    protected $dates = ['deleted_at']; 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getProductTable()
    {
        switch ($this->category_id) {
            case 1:
                return 'App\Models\Accessory'; 
            case 2:
                return 'App\Models\FireExtinguisher';
            case 3:
                return 'App\Models\FireSuppressionSystem';
            case 4:
                return 'App\Models\WatermistSystem';
            default:
                return null;
        }
    }

    public function product()
    {
        return $this->morphTo(__FUNCTION__, 'category_id', 'product_id');
    }
    public function checkoutItems()
    {
        return $this->hasMany(CheckoutItem::class, 'checkout_id');
    }

    public function items()
    {
        return $this->hasMany(CheckoutItem::class, 'checkout_id');
    }

}
