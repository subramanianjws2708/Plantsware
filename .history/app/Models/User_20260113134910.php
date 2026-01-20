<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 🔽 IMPORT RELATION MODELS
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\Order;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',   // ✅ REQUIRED for Google login
        'phone',
        'address',
        'city',
        'state',
        'pincode',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',

        // ✅ ONLY keep this if address column is JSON
        'address' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function inWishlist(Product $product)
    {
        return $this->wishlist()
                    ->where('product_id', $product->id)
                    ->exists();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
