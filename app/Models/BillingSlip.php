<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class BillingSlip extends Model
{
    use SoftDeletes;

    protected $table = 'billing_slips';

    protected $fillable = [
        'user_id',
        'year',
        'seq',
        'slip_date',
        'classification',
        'client_name',
        'address',
        'gst_number',
        'contact_no',
        'email',
        'discount',
        'total_price',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function newSeqNo($classification)
    {
        return BillingSlip::withTrashed()->where('user_id', auth()->id())->where('classification', $classification)->where('year', date('Y'))->max('seq') + 1;
    }

    public static function newSlipNo($classification)
    {
        return date('Y') . '-' . Str::padLeft(self::newSeqNo($classification), 4, '0');
    }

    public function getSlipNoAttribute()
    {
        return $this->year . '-' . Str::padLeft($this->seq, 4, '0');
    }
}
