<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const RECEIPT = 4;
    public const ELECTRONIC_BALLOT = 2;

    protected $fillable = [
        'restaurant_id',
        'table_id',
        'client_id',
        'consumption',
        'tip',
        'shipping',
        'payment_method',
        'document_type',
        'taco_data',
        'history_data',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Order::class,'final_payment_id','id');
    }
}
