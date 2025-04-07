<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile_no',
        'user_type',
        'address',
        'email',
        'password',
        'is_email_verified',
        'profile_image', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id')->where('user_type', 'provider');
    }
    
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class, 'user_id');
    }

    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'provider_id');
    // }

    public function accessories()
    {
        return $this->hasMany(Accessory::class, 'provider_id');
    }

    public function fireExtinguishers()
    {
        return $this->hasMany(FireExtinguisher::class, 'provider_id');
    }

    public function fireSuppressionSystems()
    {
        return $this->hasMany(FireSuppressionSystem::class, 'provider_id');
    }

    public function watermistSystems()
    {
        return $this->hasMany(WatermistSystem::class, 'provider_id');
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? asset('storage/' . $this->profile_image) : asset('default-profile-image.jpg');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
