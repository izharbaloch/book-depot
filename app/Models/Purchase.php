<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'user_id',
        'purchase_date',
        'invoice_number',
        'total',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total'         => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public static function generatePurchaseNumber(): string
    {
        return 'PO-' . strtoupper(substr(uniqid(), -6)) . '-' . rand(10, 99);
    }
}
