<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;



class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'status', 'business_name', 'license'];

    
public function user()
{
    return $this->belongsTo(User::class);
}

}
