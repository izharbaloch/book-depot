<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'session_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(CartItem::class)->with('product');
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn($i) => $i->price * $i->quantity);
    }

    public function getTaxAttribute(): float
    {
        return round($this->subtotal * 0.08, 2);
    }

    public function getShippingAttribute(): float
    {
        return $this->subtotal >= 150 ? 0.0 : 12.00;
    }

    public function getTotalAttribute(): float
    {
        return $this->subtotal + $this->tax + $this->shipping;
    }

    public function getItemCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public static function getForUser(): static
    {
        if (auth()->check()) {
            return static::firstOrCreate(['user_id' => auth()->id()]);
        }
        return static::firstOrCreate(['session_id' => session()->getId()]);
    }
}
