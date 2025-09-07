<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'stock_trasactions';

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'stock_qty',
        'type',
        'price',
        'dis_price',
        'discount',
        'bill_id',
        'bill_detail_id',
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
