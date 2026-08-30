<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'address', 'notes', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('name');
    }

    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn($q, $t) => $q->where(
            fn($s) => $s->where('name', 'like', "%$t%")
                ->orWhere('phone', 'like', "%$t%")
                ->orWhere('email', 'like', "%$t%")
        ));
    }
}
