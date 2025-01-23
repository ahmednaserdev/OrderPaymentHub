<?php
namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_details',
        'items',
        'total',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'user_details' => 'array',
        'status' => OrderStatus::class,
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
