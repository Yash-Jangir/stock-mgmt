<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'category_id',
        'gender',
        'age_group',
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

    protected function images(): Attribute
    {
        return Attribute::get(fn () => $this->getMedia('images') ?? collect());
    }

    public function skus()
    {
        return $this->hasMany(Sku::class, 'product_id', 'id');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}
