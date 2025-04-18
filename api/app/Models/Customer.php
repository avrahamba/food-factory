<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'location',
        'phone',
    ];

    public function orders()
    {
        return Order::where('customer_id', $this->id)->get();
    }
}
