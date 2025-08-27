<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colors';

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'color_code',
        'description',
        'rank',
        'is_active',
    ];
}
