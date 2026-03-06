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
