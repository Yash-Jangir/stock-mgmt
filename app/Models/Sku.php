<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'skus';

    protected $fillable = [
        'user_id',
        'product_id',
        'color_id',
        'size_id',
        'price',
        'description',
        'is_active',
    ];

    public function stock()
    {
        return $this->morphOne(Stock::class, 'model');
    }

    public function transaction()
    {
        return $this->morphOne(StockTransaction::class, 'model');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
