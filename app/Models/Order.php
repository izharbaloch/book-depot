<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'notes',
        'subtotal',
        'shipping',
        'tax',
        'discount',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax'      => 'decimal:2',
        'discount' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    // ── Status helpers ──────────────────────────────────────
    const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'badge-warning',
            'processing' => 'badge-info',
            'shipped'    => 'badge-primary',
            'delivered'  => 'badge-success',
            'cancelled'  => 'badge-danger',
            default      => 'badge-secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '⏳',
            'processing' => '⚙️',
            'shipped'    => '🚚',
            'delivered'  => '✅',
            'cancelled'  => '❌',
            default      => '•',
        };
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // ── Relationships ───────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class)->with('product');
    }

    // ── Scopes ─────────────────────────────────────────────
    public function scopeRecent($q, int $n = 10)
    {
        return $q->orderBy('created_at', 'desc')->limit($n);
    }

    // ── Order number generator ──────────────────────────────
    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(substr(uniqid(), -6)) . '-' . rand(10, 99);
    }
}
