<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueCounter extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'date', 'last_number'];

    protected $casts = [
        'date' => 'date',
        'last_number' => 'integer',
    ];

    /**
     * Get the category for this counter
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope for today's counters
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
