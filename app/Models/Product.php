<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'author_id',
        'publisher_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'cost_price',
        'sku',
        'isbn',
        'barcode',
        'stock',
        'min_stock_level',
        'weight',
        'image',
        'gallery',
        'is_active',
        'is_featured',
        'is_trending',
        'is_bestseller',
        'is_new',
        'rating',
        'review_count',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'sale_price'     => 'decimal:2',
        'cost_price'     => 'decimal:2',
        'weight'         => 'decimal:2',
        'rating'         => 'decimal:2',
        'gallery'        => 'array',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
        'is_trending'    => 'boolean',
        'is_bestseller'  => 'boolean',
        'is_new'         => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
    public function wishlisters()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // ── Scopes ─────────────────────────────────────────────
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeTrending($q)
    {
        return $q->where('is_trending', true)->active();
    }
    public function scopeBestseller($q)
    {
        return $q->where('is_bestseller', true)->active();
    }
    public function scopeNewArrivals($q)
    {
        return $q->where('is_new', true)->active();
    }
    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true)->active();
    }
    public function scopeOnSale($q)
    {
        return $q->whereNotNull('sale_price')->active();
    }

    public function scopeLowStock($q)
    {
        return $q->whereColumn('stock', '<=', 'min_stock_level')->where('stock', '>', 0);
    }

    public function scopeOutOfStock($q)
    {
        return $q->where('stock', '<=', 0);
    }

    // Fast lookup for admin/POS search: name, SKU, ISBN, barcode, author, publisher.
    public function scopeSearch($q, ?string $term)
    {
        return $q->when($term, fn($q, $t) => $q->where(
            fn($s) => $s->where('name', 'like', "%$t%")
                ->orWhere('sku', 'like', "%$t%")
                ->orWhere('isbn', 'like', "%$t%")
                ->orWhere('barcode', 'like', "%$t%")
                ->orWhereHas('author', fn($a) => $a->where('name', 'like', "%$t%"))
                ->orWhereHas('publisher', fn($p) => $p->where('name', 'like', "%$t%"))
        ));
    }

    public function scopeFilter($q, array $filters)
    {
        $q->active();

        if (!empty($filters['category'])) {
            $q->whereHas('category', fn($c) => $c->where('slug', $filters['category']));
        }
        if (!empty($filters['author'])) {
            $q->where('author_id', $filters['author']);
        }
        if (!empty($filters['publisher'])) {
            $q->where('publisher_id', $filters['publisher']);
        }
        if (!empty($filters['min_price'])) {
            $q->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $q->where('price', '<=', $filters['max_price']);
        }
        if (!empty($filters['sale'])) {
            $q->whereNotNull('sale_price');
        }
        if (!empty($filters['bestseller'])) {
            $q->where('is_bestseller', true);
        }
        if (!empty($filters['search'])) {
            $q->search($filters['search']);
        }

        $sort = $filters['sort'] ?? 'featured';
        match ($sort) {
            'price_asc'  => $q->orderBy('price', 'asc'),
            'price_desc' => $q->orderBy('price', 'desc'),
            'newest'     => $q->orderBy('created_at', 'desc'),
            'rating'     => $q->orderBy('rating', 'desc'),
            default      => $q->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
        };

        return $q;
    }

    // ── Accessors ──────────────────────────────────────────
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/product-placeholder.jpg');
    }

    public function getGalleryUrlsAttribute(): array
    {
        return collect($this->gallery ?? [])->map(fn($img) => asset('storage/' . $img))->toArray();
    }

    public function getCurrentPriceAttribute(): float
    {
        return (float)($this->sale_price ?? $this->price);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->is_on_sale) return 0;
        return (int)round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getBadgeAttribute(): ?string
    {
        if ($this->is_new)  return 'new';
        if ($this->is_on_sale) return 'sale';
        return null;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ── Related products ───────────────────────────────────
    public function related(int $limit = 4)
    {
        return static::active()
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
