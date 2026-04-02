<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostAndFoundItem extends Model
{
    protected $fillable = [
        'room_id',
        'booking_id',
        'staff_id',
        'item_name',
        'description',
        'location_found',
        'found_at',
        'image_path',
        'status',
        'guest_name',
        'guest_phone',
        'storage_location',
        'claimed_at',
        'claimed_by_name',
        'processed_by_staff_id',
        'reception_notes',
    ];

    protected $casts = [
        'found_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    /**
     * Get the room where the item was found
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the booking associated with the found item
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the housekeeper who found the item
     */
    public function finder(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Get the receptionist who processed the claim
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'processed_by_staff_id');
    }
}
