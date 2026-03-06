<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_month',
        'current_value',
    ];

    /**
     * Get the next assessment number for the given year and month
     * Format: YYYY-MM-NNNN (Year-Month-SequentialNumber)
     * The sequential number resets every year (not monthly)
     */
    public static function getNextNumber(string $year, string $month): string
    {
        // Use year only for the sequence key to reset yearly
        // Format: YYYY-NNNN where NNNN resets at the start of each year
        $yearOnly = $year;
        $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
        
        return \DB::transaction(function () use ($yearOnly, $monthPadded) {
            // Try to find existing sequence record for this year
            $sequence = self::where('year_month', $yearOnly)->lockForUpdate()->first();
            
            if ($sequence) {
                // Increment the current value
                $sequence->increment('current_value');
                $nextNumber = $sequence->current_value;
            } else {
                // Create new sequence record starting with 1 for this year
                $sequence = self::create([
                    'year_month' => $yearOnly,
                    'current_value' => 1,
                ]);
                $nextNumber = 1;
            }
            
            // Return format: YYYY-MM-NNNN (display includes month for better organization)
            return $yearOnly . '-' . $monthPadded . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}