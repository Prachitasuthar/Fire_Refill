<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatermistSystem extends Model
{
    use HasFactory;

    protected $fillable = [
         'coupon_id', 'category_id', 'provider_id', 'name', 'image', 'price', 'description', 
        'technology_type', 'nozzle_type', 'working_pressure', 'droplet_size', 
        'flow_rate', 'application_area', 'stock', 'warranty'
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

    // Coupon Auto Assign
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($watermistSystem) {
            $watermistSystem->category_id = 4;
            $watermistSystem->assignCoupon();
        });

        static::updating(function ($watermistSystem) {
            $watermistSystem->assignCoupon();
        });
    }

    public function assignCoupon()
    {
        $coupon = Coupon::where('category_id', $this->category_id)
            ->where('product_id', $this->id)
            ->where('provider_id', $this->provider_id)
            ->where('expiry_date', '>=', now()) 
            ->whereColumn('used_count', '<', 'max_usage') 
            ->orderBy('discount', 'desc') 
            ->first();

        if ($coupon) {
            $this->coupon_id = $coupon->id;
        } else {
            $this->coupon_id = null; 
        }
    }
}
