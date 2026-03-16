<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Inquiry;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'section', 'section_name', 'name', 'description', 'color', 'lobby', 'is_active'];

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
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
     * Generate next queue number (Section-specific)
     */
    public function generateQueueNumber(): string
    {
        return DB::transaction(function () {
            // Get all category IDs within the same section
            $categoryIdsInSection = Category::where('section', $this->section)->pluck('id');

            // Find the maximum last_number for today across all categories in this section
            $maxLastNumber = QueueCounter::whereIn('category_id', $categoryIdsInSection)
                                        ->whereDate('date', now()->toDateString())
                                        ->max('last_number') ?? 0;

            $newNumber = $maxLastNumber + 1;

            // Update the counter for the current category
            $counter = $this->getTodayCounter();
            $counter->last_number = $newNumber;
            $counter->save();

            $queueNumber = sprintf('%s-%03d', $this->code, $newNumber);

            // Fallback loop to ensure uniqueness in case of race conditions or legacy data
            while (Inquiry::where('queue_number', $queueNumber)->whereDate('date', now()->toDateString())->exists()) {
                $newNumber++;
                $queueNumber = sprintf('%s-%03d', $this->code, $newNumber);
            }

            // If the number was incremented in the loop, re-sync the counter
            if ($newNumber > $counter->last_number) {
                $counter->last_number = $newNumber;
                $counter->save();
            }

            return $queueNumber;
        });
    }
}
