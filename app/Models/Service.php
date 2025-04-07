<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'service_name',
        'service_image',
        'sub_service_name',
        'description',
        'status',
    ];

    // Relationship with User Model (Only Providers)
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id')->where('user_type', 'provider');
    }

    // Access Business Name Dynamically from service_providers Table
    public function getBusinessNameAttribute()
    {
        return $this->provider->serviceProvider->business_name ?? 'N/A';
    }

    public function subServices()
{
    return $this->hasMany(SubService::class, 'service_id');
}


    
}
