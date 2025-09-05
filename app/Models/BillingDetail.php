<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingDetail extends Model
{
    use SoftDeletes;

    protected $table = 'billing_details';

    protected $fillable = [
        'user_id',
        'billing_slip_id',
        'product_id',
        'sku_id',
        'qty',
        'unit_price',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
