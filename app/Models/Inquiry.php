<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_number',
        'guest_name',
        'address',
        'email',
        'category_id',
        'request_type',
        'purpose',
        'description',
        'priority',
        'status',
        'served_by',
        'called_at',
        'completed_at',
        'remarks',
        'forwarded_to',
        'date'
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
        'date' => 'date',
    ];

    /**
     * Get the short version of the queue number (e.g., "06")
     */
    public function getShortQueueNumberAttribute()
    {
        if (!$this->queue_number) return 'N/A';
        
        $parts = explode('-', $this->queue_number);
        $lastPart = end($parts);
        
        // Remove non-numeric characters if any
        $numberPart = preg_replace('/[^0-9]/', '', $lastPart);
        
        if ($numberPart === '') return $lastPart;
        
        // Convert to integer to remove leading zeros, then format as 2+ digits
        $num = intval($numberPart);
        
        // If the number is less than 10, format it as 2 digits (e.g. "06")
        // If it's 10 or more, it will just be "10", "101", etc.
        return sprintf('%02d', $num);
    }

    /**
     * Get the category for this inquiry
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user serving this inquiry
     */
    public function servedBy()
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    /**
     * Get the user this inquiry was forwarded to
     */
    public function forwardedTo()
    {
        return $this->belongsTo(User::class, 'forwarded_to');
    }

    /**
     * Get the assessment for this inquiry
     */
    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }

    /**
     * Scope for today's inquiries
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    /**
     * Scope for waiting inquiries
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope for serving inquiries
     */
    public function scopeServing($query)
    {
        return $query->where('status', 'serving');
    }

    /**
     * Scope for completed inquiries
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the next inquiry in a section according to priority rules (alternating FIFO)
     */
    public static function getNextInquiryInSection($section, $date = null)
    {
        $date = $date ?: now()->toDateString();
        
        $waiting = self::whereDate('date', $date)
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'waiting')
            ->select('inquiries.*')
            ->orderBy('inquiries.created_at')
            ->get();

        if ($waiting->isEmpty()) {
            return null;
        }

        // Get the currently serving inquiry in this section (if any) and last completed inquiry in this section
        $currentlyServing = self::whereDate('date', $date)
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'serving')
            ->select('inquiries.*')
            ->first();
            
        $lastServedInquiry = self::whereDate('date', $date)
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'completed')
            ->select('inquiries.*')
            ->orderBy('inquiries.completed_at', 'desc')
            ->first();

        // Determine the last served priority type
        if ($currentlyServing) {
            $lastServedType = $currentlyServing->priority;
        } else {
            $lastServedType = $lastServedInquiry ? $lastServedInquiry->priority : null;
        }

        $priorityPool = $waiting->where('priority', 'priority');
        $normalPool = $waiting->where('priority', 'normal');

        // If there are no priority inquiries, return the oldest normal inquiry
        if ($priorityPool->isEmpty()) {
            return $normalPool->first();
        }

        // If there are no normal inquiries, return the oldest priority inquiry
        if ($normalPool->isEmpty()) {
            return $priorityPool->first();
        }

        // If starting fresh (no one currently serving and no last served), 
        // pick the oldest inquiry overall to establish the starting point
        if ($lastServedType === null) {
            return $waiting->first();
        }
        
        // If last served was priority and there are normal inquiries available,
        // return the oldest normal inquiry to avoid serving two priority in a row
        if ($lastServedType === 'priority') {
            return $normalPool->first();
        }

        // Otherwise (last served was normal), return the oldest priority inquiry
        return $priorityPool->first();
    }

    /**
     * Sort inquiries within a section using the priority algorithm (alternating FIFO)
     */
    public static function sortInquiriesByPriority($inquiries, $section, $date = null)
    {
        $date = $date ?: now()->toDateString();
        
        $serving = $inquiries->where('status', 'serving')->sortBy('served_at');
        $waiting = $inquiries->where('status', 'waiting')->sortBy('created_at');
        $skipped = $inquiries->where('status', 'skipped')->sortBy('created_at');
        
        if ($waiting->isEmpty()) {
            return $serving->concat($skipped);
        }

        // Get the last completed inquiry in this section to determine the starting point
        $lastServedInquiry = self::whereDate('date', $date)
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'completed')
            ->select('inquiries.*')
            ->orderBy('inquiries.completed_at', 'desc')
            ->first();

        // If someone is currently serving, use their priority to determine next
        if ($serving->isNotEmpty()) {
            $lastServedType = $serving->last()->priority;
        } else {
            $lastServedType = $lastServedInquiry ? $lastServedInquiry->priority : null;
        }

        $priorityPool = $waiting->where('priority', 'priority')->values();
        $normalPool = $waiting->where('priority', 'normal')->values();
        
        $sortedWaiting = collect();
        $pIndex = 0;
        $nIndex = 0;
        
        // Determine starting type: if fresh, use oldest inquiry's type
        if ($lastServedType === null) {
            $firstOverall = $waiting->first();
            $nextType = $firstOverall ? $firstOverall->priority : 'normal';
        } else {
            // Alternate from the last served type
            $nextType = ($lastServedType === 'priority') ? 'normal' : 'priority';
        }

        // Simulate picking inquiries one by one alternating between pools
        while ($pIndex < $priorityPool->count() || $nIndex < $normalPool->count()) {
            if ($nextType === 'priority') {
                if ($pIndex < $priorityPool->count()) {
                    $sortedWaiting->push($priorityPool[$pIndex]);
                    $pIndex++;
                } elseif ($nIndex < $normalPool->count()) {
                    $sortedWaiting->push($normalPool[$nIndex]);
                    $nIndex++;
                }
                $nextType = 'normal';
            } else {
                if ($nIndex < $normalPool->count()) {
                    $sortedWaiting->push($normalPool[$nIndex]);
                    $nIndex++;
                } elseif ($pIndex < $priorityPool->count()) {
                    $sortedWaiting->push($priorityPool[$pIndex]);
                    $pIndex++;
                }
                $nextType = 'priority';
            }
        }

        return $serving->concat($sortedWaiting)->concat($skipped);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Mark as serving
     */
    public function markAsServing($userId)
    {
        $this->update([
            'status' => 'serving',
            'served_by' => $userId,
            'called_at' => now()
        ]);
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Mark as skipped
     */
    public function markAsSkipped()
    {
        $this->update([
            'status' => 'skipped'
        ]);
    }

    /**
     * Get processing time in minutes
     */
    public function getProcessingTimeAttribute()
    {
        if ($this->called_at && $this->completed_at) {
            return $this->called_at->diffInMinutes($this->completed_at);
        }
        return null;
    }
}
