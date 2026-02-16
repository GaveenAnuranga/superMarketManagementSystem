<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_email',
    ];

    /**
     * Get the items for the bill.
     */
    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
}
