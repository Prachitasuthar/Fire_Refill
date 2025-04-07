<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FireExtinguisher extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id', 'category_id', 'provider_id', 'name', 'image', 'price', 'description', 
        'fire_class', 'suitability', 'capacity', 'extinguishing_agent', 
        'discharge_time', 'working_pressure', 'cylinder_material', 
        'operating_temprature', 'weight', 'stock', 'warranty'
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
 
         static::creating(function ($fireExtinguisher) {
             $fireExtinguisher->category_id = 2; 
             $fireExtinguisher->assignCoupon();
         });
 
         static::updating(function ($fireExtinguisher) {
             $fireExtinguisher->assignCoupon();
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

