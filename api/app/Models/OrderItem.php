<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    protected $table = 'order_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'product_id',
        'order_id',
        'count',
    ];
}
