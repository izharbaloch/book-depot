<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'bio', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('name');
    }

    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn($q, $t) => $q->where('name', 'like', "%$t%"));
    }
}
