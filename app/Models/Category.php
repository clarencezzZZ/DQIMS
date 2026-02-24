<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Inquiry;

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
        // Use database transaction to ensure atomicity
        return DB::transaction(function () {
            $counter = $this->getTodayCounter();
            $counter->increment('last_number');
            
            $queueNumber = sprintf('%s-%03d', $this->code, $counter->last_number);
            
            // Check if this queue number already exists for today
            $existing = Inquiry::where('queue_number', $queueNumber)
                             ->whereDate('date', now()->toDateString())
                             ->exists();
            
            // If it exists, increment again and try a new one
            if ($existing) {
                $counter->increment('last_number');
                $queueNumber = sprintf('%s-%03d', $this->code, $counter->last_number);
                
                // Double check
                $existing = Inquiry::where('queue_number', $queueNumber)
                                 ->whereDate('date', now()->toDateString())
                                 ->exists();
                
                if ($existing) {
                    // Use timestamp as fallback to ensure uniqueness
                    $timestamp = now()->format('His'); // HHMMSS
                    $queueNumber = sprintf('%s-%03d-%s', $this->code, $counter->last_number, $timestamp);
                }
            }
            
            return $queueNumber;
        });
    }
}
