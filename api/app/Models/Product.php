<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'price_for_customer',
        'unit_type',
        'unit_type_customer',
        'ratio'
    ];

    protected $casts = [
        'price_for_customer' => 'float'
    ];
}
