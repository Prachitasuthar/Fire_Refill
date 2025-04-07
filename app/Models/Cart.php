<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart'; 
    protected $fillable = [
        'user_id',
        'provider_id',
        'category_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function getProductAttribute()
    {
        switch ($this->category_id) {
            case 1:
                return \App\Models\Accessory::find($this->product_id);
            case 2:
                return \App\Models\FireExtinguisher::find($this->product_id);
            case 3:
                return \App\Models\FireSuppressionSystem::find($this->product_id);
            case 4:
                return \App\Models\WatermistSystem::find($this->product_id);
            default:
                return null;
        }
    }
}
