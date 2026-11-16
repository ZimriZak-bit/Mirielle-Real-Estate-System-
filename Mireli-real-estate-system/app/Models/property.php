<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    // These are the "frozen values" from your documentation
    const AVAILABLE = 'available';
    const RESERVED = 'reserved';
    const SOLD = 'sold';

    protected $fillable = [
        'title', 'location', 'bedrooms', 'size', 'amenities', 
        'description', 'price', 'deposit_amount', 'status', 'buyer_id'
    ];

    // Relationship: A property has many images
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    // Relationship: The client who bought/reserved it
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // --- STATUS HELPERS (Interface I3) ---

    public function isAvailable()
    {
        return $this->status === self::AVAILABLE;
    }

    public function markReserved(User $buyer)
    {
        $this->status = self::RESERVED;
        $this->buyer_id = $buyer->id;
        $this->save();
    }

    public function markSold()
    {
        $this->status = self::SOLD;
        $this->save();
    }

    public function markAvailable()
    {
        $this->status = self::AVAILABLE;
        $this->buyer_id = null;
        $this->save();
    }
}