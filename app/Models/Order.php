<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'shein_order_number',
        'amount',
        'tracking_number',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}