<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'product_id', 'order_count', 'client_id', 'status', 'assigned_table_id', 'final_payment_id', 'comment', 'deliver_status', 'direct'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function children() {
        return $this->hasMany(SubOrder::class, 'order_id', 'id')->with('detail');
    }
}
