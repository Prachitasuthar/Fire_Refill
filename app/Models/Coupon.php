<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_code',
        'provider_id',
        'category_id',
        'product_id',
        'price',
        'discount',
        'final_price',
        'expiry_date',
        'max_usage',
        'used_count'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isExpired()
    {
        return now()->greaterThan($this->expiry_date) || $this->used_count >= $this->max_usage;
    }

    // Coupon Auto Assign to Accessories, Fire Extinguishers, Fire Suppression Systems & Watermist Systems
    protected static function boot()
    {
        parent::boot();

        static::created(function ($coupon) {
            if ($coupon->category_id == 1) { // Accessories ke liye
                DB::table('accessories')
                    ->where('id', $coupon->product_id)
                    ->where('provider_id', $coupon->provider_id)
                    ->update(['coupon_id' => $coupon->id]);
            } elseif ($coupon->category_id == 2) { // Fire Extinguishers ke liye
                DB::table('fire_extinguishers')
                    ->where('id', $coupon->product_id)
                    ->where('provider_id', $coupon->provider_id)
                    ->update(['coupon_id' => $coupon->id]);
            } elseif ($coupon->category_id == 3) { // Fire Suppression Systems ke liye
                DB::table('fire_suppression_systems')
                    ->where('id', $coupon->product_id)
                    ->where('provider_id', $coupon->provider_id)
                    ->update(['coupon_id' => $coupon->id]);
            } elseif ($coupon->category_id == 4) { // Watermist Systems ke liye
                DB::table('watermist_systems')
                    ->where('id', $coupon->product_id)
                    ->where('provider_id', $coupon->provider_id)
                    ->update(['coupon_id' => $coupon->id]);
            }
        });

        static::deleting(function ($coupon) {
            if ($coupon->category_id == 1) {
                DB::table('accessories')->where('coupon_id', $coupon->id)->update(['coupon_id' => null]);
            } elseif ($coupon->category_id == 2) {
                DB::table('fire_extinguishers')->where('coupon_id', $coupon->id)->update(['coupon_id' => null]);
            } elseif ($coupon->category_id == 3) {
                DB::table('fire_suppression_systems')->where('coupon_id', $coupon->id)->update(['coupon_id' => null]);
            } elseif ($coupon->category_id == 4) {
                DB::table('watermist_systems')->where('coupon_id', $coupon->id)->update(['coupon_id' => null]);
            }
        });
    }
}
