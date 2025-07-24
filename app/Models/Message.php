<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'user_id',
        'content',
        'image_path',
    ];

    // Relación con el usuario que envió el mensaje
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la transacción
    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }
}