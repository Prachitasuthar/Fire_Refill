<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckoutItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'checkout_id',
        'product_id',
        'provider_id',
        'category_id',
        'quantity',
        'price',
        'final_price',
        'tracking_status',
        'deleted_at',
    ];

    protected $dates = ['deleted_at']; 

    public function checkout()
    {
        return $this->belongsTo(Checkout::class, 'checkout_id');
    }
    public function product()
    {
        return $this->morphTo();
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('deleted_at', false);
    }
}
