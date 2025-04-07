<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }

    public function fireExtinguishers()
    {
        return $this->hasMany(FireExtinguisher::class);
    }

    public function fireSuppressionSystems()
    {
        return $this->hasMany(FireSuppressionSystem::class);
    }

    public function watermistSystems()
    {
        return $this->hasMany(WatermistSystem::class);
    }
}

