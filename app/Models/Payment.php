<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'table_id', 'client_id', 'consumption', 'tip', 'shipping', 'payment_method', 'document_type'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }
}
