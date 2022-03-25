<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'status'];

    public function detail()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
