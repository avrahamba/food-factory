<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'customer_name',
        'price',
        'customer_phone',
        'date'
    ];

    protected $casts = [
        'price' => 'float'
    ];

    public function orderItems()
    {
        return OrderItem::where('order_id', $this->id)->get();
    }

    public function calcPrice()
    {
        $sql = '
        SELECT SUM(products.price_for_customer * order_items.count) as price ' .
            ' FROM order_items ' .
            ' join products on order_items.product_id = products.id ' .
            'WHERE order_items.order_id = ' . $this->id;
        return DB::select($sql)[0]->price;
    }
}
