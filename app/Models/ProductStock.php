<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'available',
        'on_hand',
        'reserved',
    ];

    public function stockable()
    {
        return $this->morphTo();
    }
}
