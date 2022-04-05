<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'tax_id',
        'rut',
        'slogan',
        'address',
        'bank_transfer_details',
        'owner_id',
        'is_receipt_sii',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function tables()
    {
        return $this->hasMany(Table::class, 'restaurant_id', 'id');
    }
}
