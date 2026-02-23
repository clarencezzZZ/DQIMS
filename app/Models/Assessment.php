<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_number',
        'bill_number',
        'responsibility_center',
        'inquiry_id',
        'queue_number',
        'guest_name',
        'address',
        'legal_basis',
        'category_id',
        'reference',
        'request_type',
        'names_detail',
        'fees',
        'remarks',
        'processed_by',
        'assessment_date'
    ];

    protected $casts = [
        'fees' => 'decimal:2',
        'assessment_date' => 'date',
        'names_detail' => 'array',
    ];

    /**
     * Get the inquiry for this assessment
     */
    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Get the category for this assessment
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who processed this assessment
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for today's assessments
     */
    public function scopeToday($query)
    {
        return $query->whereDate('assessment_date', now()->toDateString());
    }

    /**
     * Scope by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('assessment_date', [$startDate, $endDate]);
    }

    /**
     * Generate assessment number
     */
    public static function generateAssessmentNumber(): string
    {
        $prefix = 'ASM';
        $date = now()->format('Ymd');
        $count = self::whereDate('assessment_date', now()->toDateString())->count() + 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }
}
