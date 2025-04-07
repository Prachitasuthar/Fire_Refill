<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id', 'category_id', 'provider_id','name', 'image', 'price', 'description', 
        'weight', 'power_source', 'operating_voltage', 'material', 
        'working_temprature', 'IP_routing', 'stock', 'warranty', 'coupon_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id')->where('user_type', 'provider');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($accessory) {
            $accessory->category_id = 1;
            $accessory->assignCoupon();
        });

        static::updating(function ($accessory) {
            $accessory->assignCoupon();
        });
    }

    public function assignCoupon()
    {
        $coupon = Coupon::where('category_id', $this->category_id)
            ->where('product_id', $this->id)
            ->where('provider_id', $this->provider_id)
            ->where('expiry_date', '>=', now()) 
            ->whereColumn('used_count', '<', 'max_usage') 
            ->orderBy('discount', 'asc') 
            ->first();

        $this->coupon_id = $coupon ? $coupon->id : null;
    }
}
