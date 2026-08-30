<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'phone', 'email', 'address', 'notes', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn($q, $t) => $q->where(
            fn($s) => $s->where('name', 'like', "%$t%")
                ->orWhere('phone', 'like', "%$t%")
                ->orWhere('email', 'like', "%$t%")
        ));
    }

    public function getTotalOrdersAttribute(): int
    {
        return $this->orders_count ?? $this->orders()->count();
    }

    public function getTotalSalesAttribute(): int
    {
        return $this->sales_count ?? $this->sales()->count();
    }

    public function getTotalSpentAttribute(): float
    {
        return (float) (($this->orders_sum_total ?? $this->orders()->sum('total'))
            + ($this->sales_sum_total ?? $this->sales()->sum('total')));
    }

    public static function findOrCreateByEmail(string $name, ?string $email, ?string $phone = null, ?string $address = null): self
    {
        if ($email) {
            $customer = static::firstWhere('email', $email);
            if ($customer) {
                return $customer;
            }
        }

        return static::create([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'address' => $address,
        ]);
    }
}
