<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'stock_qty',
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
