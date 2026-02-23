<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'section', 'name', 'description', 'color', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get inquiries for this category
     */
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    /**
     * Get assessments for this category
     */
    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Get queue counters for this category
     */
    public function queueCounters()
    {
        return $this->hasMany(QueueCounter::class);
    }

    /**
     * Get users assigned to this category
     */
    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'assigned_category_id');
    }

    /**
     * Get the current queue counter for today
     */
    public function getTodayCounter()
    {
        return $this->queueCounters()
            ->firstOrCreate(
                ['date' => now()->toDateString()],
                ['last_number' => 0]
            );
    }

    /**
     * Generate next queue number
     */
    public function generateQueueNumber(): string
    {
        $counter = $this->getTodayCounter();
        $counter->increment('last_number');
        
        return sprintf('%s-%03d', $this->code, $counter->last_number);
    }
}
